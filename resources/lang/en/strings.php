<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Strings Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in strings throughout the system.
    | Regardless where it is placed, a string can be listed here so it is easily
    | found in a intuitive way.
    |
    */
    'backend' => [
        'dashboard'         => [
            'dashboard'         => 'Dashboard',
            'new_users'         => 'New Users',
            'users'             => 'Users',
            'reports'           => 'Reports',
            'profiles_reported' => 'Profiles reported',
            'matches'           => 'Matches',
            'jobs'              => 'Jobs',
            'failed_jobs'       => 'Failed Jobs'
        ],
        'welcome'           => '<p>Welcome Simon</p>',
        'general'           => [
            'status' => [
                'online'  => 'Online',
                'offline' => 'Offline',
            ],
        ],
        'users'             => [
            'users'          => 'Users',
            'all_users'      => 'All Users',
            'add_new'        => 'Add New',
            'edit_user'      => 'Edit User',
            'delete_user'    => 'Delete User',
            'reported_users' => 'Reported Users',
            'create'         => [
                'name'                               => [
                    'full_name'       => 'Full Name',
                    'enter_full_name' => 'Enter full name',
                ],
                'phone'                              => [
                    'country_code'       => 'Code',
                    'phone_number'       => 'Phone Number',
                    'enter_phone_number' => 'Enter phone number',
                ],
                'password'                           => [
                    'password'            => 'Password',
                    'enter_your_password' => 'Enter your password',
                ],
                'email'                              => [
                    'email'            => 'Email',
                    'enter_your_email' => 'Enter your email',
                ],
                'gender'                             => [
                    'gender' => 'Gender',
                    'male'   => 'Male',
                    'female' => 'Female',
                ],
                'date_of_birth'                      => [
                    'date_of_birth'               => 'Date of birth',
                    'please_select_date_of_birth' => 'Please select date of birth'
                ],
                'about'                              => 'About',
                'school'                             => 'School',
                'work'                               => 'Work',
                'status'                             => 'Status',
                'role'                               => [
                    'role'          => 'Role',
                    'administrator' => 'Administrator',
                    'basic_user'    => 'Basic User'
                ],
                'save'                               => 'Save',
                'user_has_been_created_successfully' => 'User has been created successfully.'
            ],
            'edit'           => [
                'user_not_found'                    => 'User not found',
                'user_has_been_edited_successfully' => 'User has been edited successfully.'
            ],
            'delete'         => [
                'user_soft_delete'               => 'User has been temporarily deleted.',
                'user_hard_delete'               => 'User has been permanently deleted.',
                'user_soft_delete_message'       => 'The user\'s account will not be visible to others.',
                'user_hard_delete_message'       => 'All user\'s details including settings, profile details, images, matches and chats
            will be permanently deleted.',
                'delete_btn'                     => 'Delete',
                'close_btn'                      => 'Close',
                'temporary_delete_modal_heading' => 'Temporary delete',
                'permanent_delete_modal_heading' => 'Permanent delete'
            ]
        ],
        'reports'           => [
            'reports' => 'Reports',
            'users'   => 'Users'
        ],
        'images'            => [
            'image'                => 'Image',
            'images'               => 'Images',
            'image_number'         => 'Image Number',
            'edit'                 => [
                'edit_image'                 => 'Edit Image',
                'image_changed_successfully' => 'Image has been changed successfully.',
                'image_changed_failed'       => 'Image could not be changed.'
            ],
            'delete'               => [
                'delete_image'                               => 'Delete Image',
                'delete_btn'                                 => 'Delete',
                'close_btn'                                  => 'Close',
                'image_deleted_successfully'                 => 'Image has been deleted successfully.',
                'image_delete_failed'                        => 'Image could not be deleted.',
                'are_you_sure_you_want_to_delete_this_image' => 'Are you sure you want to delete this image?',
                'this_will_delete_image_number'              => 'This will delete image number',
            ],
            'drop_image_to_upload' => 'Drop image here or click to upload or change.',
            'please_select_image'  => 'Please select image'
        ],
        'settings'          => [
            'setting'            => 'Setting',
            'edit'               => [
                'edit_setting'                           => 'Edit Setting',
                'search_distance'                        => 'Search Distance',
                'the_distance_is_in_mile'                => 'The distance is in Mile.',
                'friendship'                             => 'Friendship',
                'relationship'                           => 'Relationship',
                'casual_meetup'                          => 'Casual Meetup',
                'interested_in'                          => 'Interested In',
                'you_want_to_date_with'                  => 'You want to date with',
                'female'                                 => 'Female',
                'male'                                   => 'Male',
                'both'                                   => 'Both',
                'show_ages'                              => 'Show Ages',
                'show_my_distance'                       => 'Show my distance',
                'show_my_age'                            => 'Show my age',
                'submit_btn'                             => 'Submit',
                'cancel_btn'                             => 'Cancel',
                'settings_has_been_changed_successfully' => 'Settings has been changed successfully.'
            ],
            'validation_message' => [
                'the_age_range_is_invalid'                           => 'The age range is invalid.',
                'the_minimum_age_cannot_be_greater_than_maximum_age' => 'The minimum age cannot be greater than maximum age.'
            ]
        ],
        'locations'         => [
            'location'           => 'Location',
            'edit'               => [
                'edit_location'                          => 'Edit Location',
                'location_has_been_changed_successfully' => 'Location has been changed successfully.'
            ],
            'validation_message' => [
                'please_select_marker_on_the_map' => 'Please select marker on the map. This is the default location'
            ]
        ],
        'plans'             => [
            'plans'       => 'Plans',
            'plan'        => 'Plan',
            'all_plans'   => 'All Plans',
            'add_new'     => 'Add New',
            'edit_plan'   => 'Edit Plan',
            'delete_plan' => 'Delete Plan',
            'create'      => [
                'name'                               => [
                    'name'       => 'Name',
                    'enter_name' => 'Enter name',
                ],
                'google_product_id'                  => [
                    'google_product_id'       => 'Google Product ID',
                    'enter_google_product_id' => 'Enter Google Product ID',
                ],
                'apple_product_id'                   => [
                    'apple_product_id'       => 'Apple Product ID',
                    'enter_apple_product_id' => 'Enter Apple Product ID',
                ],
                'plan_code'                          => [
                    'plan_code'       => 'Plan code',
                    'enter_plan_code' => 'Enter plan code',
                ],
                'price'                              => [
                    'price'       => 'Price',
                    'enter_price' => 'Enter Price',
                ],
                'interval'                           => [
                    'interval'       => 'Interval',
                    'enter_interval' => 'Enter interval'
                ],
                'interval_count'                     => [
                    'interval_count'       => 'Interval Count',
                    'enter_interval_count' => 'Enter interval count'
                ],
                'description'                        => 'Description',
                'save'                               => 'Save',
                'plan_has_been_created_successfully' => 'Plan has been created successfully.'
            ],
            'edit'        => [
                'plan_not_found'                    => 'Plan not found',
                'plan_has_been_edited_successfully' => 'Plan has been edited successfully.'
            ],
            'delete'      => [
                'delete_btn'                       => 'Delete',
                'close_btn'                        => 'Close',
                'plan_delete'                      => 'Plan has been permanently deleted.',
                'are_you_sure_to_delete_this_plan' => 'Are you sure to delete this plan?'
            ],
            'alert_text'  => 'Creating, Updating or Deleting Plans will not make changes to Android or IOS apps. You will need to contact
            the Android and IOS developers in order to change the plans in the app.'
        ],
        'plan_subscription' => [
            'subscriptions'       => 'Subscriptions',
            'subscription'        => 'Subscription',
            'all_subscriptions'   => 'All Subscriptions',
            'add_new'             => 'Add New',
            'edit_subscription'   => 'Edit Subscription',
            'create_subscription' => 'Create Subscription',
            'delete_subscription' => 'Delete Subscription',
            'edit'                => [
                'plan_id'   => [
                    'plan_id' => 'Plan ID',
                ],
                'starts_at' => [
                    'starts_at' => 'Starts At',
                ],
                'ends_at'   => [
                    'ends_at' => 'Ends At',
                ],

                'save'                                      => 'Save',
                'subscription_has_been_edited_successfully' => 'Subscription has been edited successfully.'

            ],
            'create'              => [
                'subscription_has_been_added_successfully' => 'Subscription has been saved successfully'
            ],
            'delete'              => [
                'delete_btn'                               => 'Delete',
                'close_btn'                                => 'Close',
                'subscription_delete'                      => 'Subscription has been permanently deleted.',
                'are_you_sure_to_delete_this_subscription' => 'Are you sure to delete this subscription?'
            ]
        ]
    ]
];