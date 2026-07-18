/**
 * Vanilla-JS port of TxT-Sanitizer's sanitize() engine
 * (TxT-Sanitizer/src/lib/sanitizer.ts). Pure string logic, no framework deps.
 */
(function () {
    function hasAlpha(str) {
        return /[a-zA-Z]/.test(str);
    }

    function escapeRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function isStructural(find, replace) {
        var alphaOnly = function (s) {
            return s.replace(/[^a-zA-Z]/g, '').toLowerCase();
        };
        return alphaOnly(find) === alphaOnly(replace);
    }

    function applyCase(matched, template) {
        var matchedAlpha = matched.replace(/[^a-zA-Z]/g, '');
        var alphaIdx = 0;
        return template
            .split('')
            .map(function (ch) {
                if (/[a-zA-Z]/.test(ch)) {
                    var src = matchedAlpha[alphaIdx] !== undefined ? matchedAlpha[alphaIdx] : ch;
                    alphaIdx++;
                    return src === src.toUpperCase() ? ch.toUpperCase() : ch.toLowerCase();
                }
                return ch;
            })
            .join('');
    }

    /**
     * @param {string} text
     * @param {Array<{priority:number, find:string, replace:string}>} rules
     * @returns {{output: string, matches: Array<{original:string, replaced:string, rulePriority:number}>}}
     */
    function sanitize(text, rules) {
        if (!text || !rules || rules.length === 0) {
            return { output: text || '', matches: [] };
        }

        var sorted = rules.slice().sort(function (a, b) {
            return a.priority - b.priority;
        });

        var segments = [{ text: text }];

        sorted.forEach(function (rule) {
            if (!rule.find) return;

            var newSegments = [];

            if (!hasAlpha(rule.find)) {
                segments.forEach(function (seg) {
                    var parts = seg.text.split(rule.find);
                    if (parts.length === 1) {
                        newSegments.push(seg);
                        return;
                    }
                    for (var i = 0; i < parts.length; i++) {
                        if (parts[i].length > 0) {
                            newSegments.push({ text: parts[i], match: seg.match });
                        }
                        if (i < parts.length - 1) {
                            newSegments.push({
                                text: rule.replace,
                                match: { original: rule.find, rulePriority: rule.priority },
                            });
                        }
                    }
                });
            } else {
                var regex = new RegExp(escapeRegex(rule.find), 'gi');
                var structural = isStructural(rule.find, rule.replace);

                segments.forEach(function (seg) {
                    var segText = seg.text;
                    var cursor = 0;
                    var regexMatch;
                    var hadMatch = false;
                    regex.lastIndex = 0;

                    while ((regexMatch = regex.exec(segText)) !== null) {
                        hadMatch = true;
                        var matchedText = regexMatch[0];
                        var matchStart = regexMatch.index;
                        var matchEnd = matchStart + matchedText.length;

                        if (matchStart > cursor) {
                            newSegments.push({ text: segText.slice(cursor, matchStart), match: seg.match });
                        }

                        var actualReplacement = structural ? applyCase(matchedText, rule.replace) : rule.replace;

                        newSegments.push({
                            text: actualReplacement,
                            match: { original: matchedText, rulePriority: rule.priority },
                        });

                        cursor = matchEnd;
                        if (regexMatch.index === regex.lastIndex) {
                            regex.lastIndex++;
                        }
                    }

                    if (!hadMatch) {
                        newSegments.push(seg);
                    } else if (cursor < segText.length) {
                        newSegments.push({ text: segText.slice(cursor), match: seg.match });
                    }
                });
            }

            segments = newSegments;
        });

        var output = '';
        var matches = [];

        segments.forEach(function (seg) {
            output += seg.text;
            if (seg.match) {
                matches.push({
                    original: seg.match.original,
                    replaced: seg.text,
                    rulePriority: seg.match.rulePriority,
                });
            }
        });

        return { output: output, matches: matches };
    }

    function countWords(text) {
        var trimmed = (text || '').trim();
        return trimmed ? trimmed.split(/\s+/).length : 0;
    }

    function countChars(text) {
        return (text || '').length;
    }

    window.sanitizeFiverrText = function (text) {
        return sanitize(text, window.FIVERR_SANITIZER_RULES || []);
    };
    window.countFiverrWords = countWords;
    window.countFiverrChars = countChars;
})();
