<?php

namespace App\Providers;

use App\Models\Chat;
use App\Models\Event;
use App\Models\QuickAccessMenu;
use App\Models\SettingAdminSite;
use App\Models\User;
use App\Notifications\AdminNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->is_admin) {
                return true; // Allows Admin to bypass all permission checks
            }
        });

        View::composer('*', function ($view) {
            // ================================================
            // Greetings ======================================
            // ================================================

            $currentHour = now()->hour;
            if ($currentHour < 12) {
                $greetings = "Good Morning!";
            } elseif ($currentHour < 18) {
                $greetings = "Good Afternoon!";
            } else {
                $greetings = "Good Evening!";
            }

            // ================================================
            // Events and upcoming events =====================
            // ================================================

            $today = Carbon::today();
            $eventMessages = []; // Initialize event messages array

            // Check if today is the user's birthday
            if (Auth::check() && Auth::user()->dob) {
                $userDob = Carbon::parse(Auth::user()->dob);
                $userAge = Carbon::parse(Auth::user()->dob)->age + 1;

                if ($userDob->isBirthday()) {
                    $eventMessages[] = (object) [
                        'name'    => 'Birthday',
                        'message' => "Happy Birthday! 🎉 Congratulations, you have turned {$userAge}!",
                    ];
                }
            }

            // Check if today is any other user's birthday
            $users = User::whereNotNull('dob')->get();
            foreach ($users as $user) {
                $userDob = Carbon::parse($user->dob);
                if ($userDob->isBirthday() && Auth::id() != $user->id) {
                    $eventMessages[] = (object) [
                        'name'    => "Birthday of " . $user->name,
                        'message' => "Happy Birthday to {$user->name}! 🎉",
                    ];
                }
            }

            // Fetch all events happening today
            $todayEvents = Event::whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->get();

            foreach ($todayEvents as $event) {
                $eventMessages[] = (object) [
                    'name'    => $event->name,
                    'message' => $event->message,
                ];
            }


            // ==================================================================
            // Upcoming Events and birthdays ==================================
            // ==================================================================

            // Fetch upcoming events within the next 7 days, including birthdays
            $upcomingEvents = Event::where('start_date', '>', $today)
                ->where('start_date', '<=', $today->copy()->addDays(7))
                ->orderBy('start_date')
                ->get();

            // Check if a birthday is within the next 7 days
            if (Auth::check() && Auth::user()->dob) {
                $userDob = Carbon::parse(Auth::user()->dob);
                $birthdayThisYear = $userDob->year($today->year);

                if ($birthdayThisYear->isFuture() && $birthdayThisYear->diffInDays($today) <= 7) {
                    $upcomingEvents->push((object) [
                        'name'    => 'Birthday',
                        'message' => "Your birthday is coming soon! 🎂",
                        'start_date' => $birthdayThisYear->toDateString(),
                    ]);
                }
            }

            // Check if any user's birthday is within the next 7 days
            $usersWithBirthdays = User::whereNotNull('dob')->get();

            foreach ($usersWithBirthdays as $user) {
                $userDob = Carbon::parse($user->dob);
                $birthdayThisYear = Carbon::createFromDate($today->year, $userDob->month, $userDob->day);

                // If the birthday is within the next 7 days and is in the future
                if (Auth::id() != $user->id && $birthdayThisYear->isFuture() && $birthdayThisYear->diffInDays($today) <= 7) {
                    $upcomingEvents->push((object) [
                        'name'    => 'Birthday of ' . $user->name,
                        'message' => "Birthday of {$user->name} is coming soon! 🎂",
                        'start_date' => $birthdayThisYear->toDateString(),
                    ]);
                }
            }


            // ================================================================
            // Quick Access Menus for logged in users only ======================
            // =================================================================

            if (Auth::check()) {
                $menuItems = QuickAccessMenu::where('user_id', Auth::id())->get();
                $view->with('menuItems', $menuItems);
            }

            // ==============================================================
            // Send notification if user have unread messages ==============
            // ==============================================================
            if (Auth::check()) {
                $unreadMessages = Chat::where('receiver_id', Auth::id())->where('read', false)->count();

                if ($unreadMessages > 0) {
                    // Check if the notification already exists
                    $existingNotification = Auth::user()->notifications()
                        ->whereJsonContains('data', ['title' => 'New Message'])
                        ->first();

                    if ($existingNotification) {
                        // Update the existing notification message instead of creating a new one
                        $existingNotification->update([
                            'data' => [
                                'title' => 'New Message',
                                'message' => 'You have ' . $unreadMessages . ' unread messages',
                                'type' => 'warning',
                                'icon' => 'mdi mdi-message-badge-outline',
                            ],
                        ]);
                    } else {
                        // If no existing notification, create a new one
                        Auth::user()->notify(new AdminNotification(
                            'New Message',
                            'You have ' . $unreadMessages . ' unread messages',
                            'warning',
                            'ri-chat-ai-line'
                        ));
                    }
                } else {
                    // Remove only unread message notifications
                    Auth::user()->notifications()
                        ->whereJsonContains('data', ['title' => 'New Message'])
                        ->delete();
                }
            }




            // ================================================================
            // Profile completeness ===========================================
            // =================================================================

            $profileComplete = true;
            if (Auth::check()) {
                $authUser = Auth::user();
                $profileComplete = filled($authUser->phone)
                    && filled($authUser->address)
                    && filled($authUser->designation)
                    && filled($authUser->dob);
            }

            // ================================================================
            // Pass variables to all views ===============================
            // =================================================================

            $admin = SettingAdminSite::first();
            $view->with('admin', $admin);
            $view->with('greetings', $greetings);
            $view->with('eventMessages', $eventMessages);
            $view->with('upcomingEvents', $upcomingEvents);
            $view->with('profileComplete', $profileComplete);
        });
    }
}
