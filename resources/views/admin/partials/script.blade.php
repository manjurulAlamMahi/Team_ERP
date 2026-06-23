<!-- Vendor js -->
<script src="{{ asset('admin') }}/assets/js/vendor.min.js"></script>

<!-- Daterangepicker js -->
<script src="{{ asset('admin') }}/assets/vendor/daterangepicker/moment.min.js"></script>
<script src="{{ asset('admin') }}/assets/vendor/daterangepicker/daterangepicker.js"></script>

<!-- Apex Charts js -->
<script src="{{ asset('admin') }}/assets/vendor/apexcharts/apexcharts.min.js"></script>

<!-- Vector Map js -->
<script src="{{ asset('admin') }}/assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js">
</script>
<script
    src="{{ asset('admin') }}/assets/vendor/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js">
</script>

<!-- Dashboard App js -->
<script src="{{ asset('admin') }}/assets/js/pages/demo.dashboard.js"></script>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- App js -->
<script src="{{ asset('admin') }}/assets/js/app.min.js"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

<script type="text/javascript">
    setInterval(function() {
        var currentTime = new Date();
        var currentHours = currentTime.getHours();
        var currentMinutes = currentTime.getMinutes();
        var currentSeconds = currentTime.getSeconds();
        currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
        currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
        var timeOfDay = currentHours < 12 ? "AM" : "PM";
        currentHours = currentHours > 12 ? currentHours - 12 : currentHours;
        currentHours = currentHours == 0 ? 12 : currentHours;
        var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
        document.getElementById("timer").innerHTML = currentTimeString;
    }, 1000);

    $(document).ready(function() {
        function applyThemeStyles() {
            var theme = $('html').attr('data-bs-theme');

            if (theme === 'dark') {
                $(".dashboard-date").css({
                    "color": "#fff",
                    "background": "#4254ba"
                });
                $(".dashboard-date i").css("color", "#fff");
            } else {
                $(".dashboard-date").css({
                    "color": "#333",
                    "background": "#f8f9fa"
                });
                $(".dashboard-date i").css("color", "#007bff");
            }
        }

        applyThemeStyles();

        $("#light-dark-mode").click(function() {


            applyThemeStyles();
        });
    });
</script>

<script>
    $(document).ready(function() {
        $("#top-search").on("keyup", function() {
            let searchText = $(this).val().toLowerCase().trim();
            let dropdown = $("#search-dropdown");
            let found = false;

            $(".search-item").each(function() {
                let itemName = $(this).text().toLowerCase();

                if (searchText.length < 3) {
                    $(this).hide();
                    $("#search-access").show();
                    $("#no-links").hide();
                    return;
                }
                if (itemName.includes(searchText)) {
                    $(this).show();
                    $("#search-access").hide();
                    $("#no-links").hide();
                    found = true;
                } else {
                    $(this).hide();
                    $("#search-access").hide();
                    $("#no-links").show();
                }
            });

            if (!found && searchText.length >= 3) {
                $("#no-links").show();
            } else {
                $("#no-links").hide();
            }

            dropdown.show();
        });
        $("#mobile-search").on("keyup", function() {
            let searchText = $(this).val().toLowerCase().trim();
            let dropdown = $(".mobile-quick-links-content");
            let noLinks = $("#no-mov-links");
            let found = false;

            $(".search-mobile-item").each(function() {
                let itemName = $(this).text().toLowerCase();

                if (searchText.length < 3) {
                    $(this).hide();
                    noLinks.hide();
                    dropdown.hide();
                    return;
                }

                if (itemName.includes(searchText)) {
                    $(this).show();
                    found = true;
                } else {
                    $(this).hide();
                }
            });

            if (searchText.length >= 3) {
                dropdown.show();
                noLinks.toggle(!found); // Show "No Links Found" only if no items are found
            } else {
                dropdown.hide();
            }
        });

        // $(document).on("click", function(e) {
        //     if (!$(e.target).closest(".app-search").length) {
        //         $("#search-dropdown").hide();
        //     }
        // });
    });
</script>

<!-- Sweet Alert Script -->
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'bottom-start',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })

    @if (session('success'))
        Toast.fire({
            icon: 'success',
            title: '{{ session('success') }}',
        })
    @endif

    @if (session('error'))
        Toast.fire({
            icon: 'error',
            title: '{{ session('error') }}',
        })
    @endif
</script>
<script>
    @if (session('email_success'))
        Swal.fire({
            title: "{{ session('email_success') }}!",
            text: "Please Check Your Email Address!",
            icon: "success",
            draggable: true
        });
    @endif
</script>
<!-- Sweet Alert Script -->

<script>
    function markAsRead(notificationId) {
        alert();
        $.ajax({
            url: 'dashboard/mark-notification-read',
            method: 'POST',
            data: {
                notification_id: notificationId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Toast.fire({
                    icon: 'success',
                    title: 'Notification marked as read!',
                })
            },
            error: function(error) {
                console.error('Error marking notification as read:', error);
            }
        });
    }
</script>

<script>
    $(document).ready(function() {
        // When the link is clicked
        $('#quick-access-link').on('click', function() {
            // Get the data-icon value from the link
            var iconValue = $(this).data('icon');

            // Set the value of the icon input field
            $('#icon-input').val(iconValue);
        });
    });
</script>

@stack('script')
