<?php

namespace App\Providers;

use App\Models\AppleInAppSubscription;
use App\Models\BoostProfile;
use App\Models\ChatMessage;
use App\Models\ChatMessageRecipient;
use App\Models\EmailVerification;
use App\Models\GoogleInAppSubscription;
use App\Models\Image;
use App\Models\Location;
use App\Models\PhoneVerification;
use App\Models\ReportUser;
use App\Models\RightLeftSwipe;
use App\Models\Setting;
use App\Models\Sound;
use App\Models\SwipeMatch;
use App\Models\User;
use App\Models\Video;
use App\Repositories\Contracts\AppleInAppSubscriptionRepository;
use App\Repositories\Contracts\BoostProfileRepository;
use App\Repositories\Contracts\ChatMessageRecipientRepository;
use App\Repositories\Contracts\ChatMessageRepository;
use App\Repositories\Contracts\EmailVerificationRepository;
use App\Repositories\Contracts\GoogleInAppSubscriptionRepository;
use App\Repositories\Contracts\ImageRepository;
use App\Repositories\Contracts\LocationRepository;
use App\Repositories\Contracts\PhoneVerificationRepository;
use App\Repositories\Contracts\PlanRepository;
use App\Repositories\Contracts\ReportUserRepository;
use App\Repositories\Contracts\RightLeftSwipeRepository;
use App\Repositories\Contracts\SettingRepository;
use App\Repositories\Contracts\SoundRepository;
use App\Repositories\Contracts\SwipeMatchRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\VideoRepository;
use App\Repositories\EloquentAppleInAppSubscriptionRepository;
use App\Repositories\EloquentBoostProfileRepository;
use App\Repositories\EloquentChatMessageRecipientRepository;
use App\Repositories\EloquentChatMessageRepository;
use App\Repositories\EloquentEmailVerificationRepository;
use App\Repositories\EloquentGoogleInAppSubscriptionRepository;
use App\Repositories\EloquentImageRepository;
use App\Repositories\EloquentLocationRepository;
use App\Repositories\EloquentPhoneVerificationRepository;
use App\Repositories\EloquentPlanRepository;
use App\Repositories\EloquentReportUserRepository;
use App\Repositories\EloquentRightLeftRepository;
use App\Repositories\EloquentSettingRepository;
use App\Repositories\EloquentSoundRepository;
use App\Repositories\EloquentSwipeMatchRepository;
use App\Repositories\EloquentUserRepository;
use App\Repositories\EloquentVideoRepository;
use Gerardojbaez\Laraplans\Models\Plan;
use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepository::class, function () {
            return new EloquentUserRepository(new User());
        });

        $this->app->bind(EmailVerificationRepository::class, function () {
            return new EloquentEmailVerificationRepository(new EmailVerification());
        });

        $this->app->bind(PhoneVerificationRepository::class, function () {
            return new EloquentPhoneVerificationRepository(new PhoneVerification());
        });

        $this->app->bind(SettingRepository::class, function () {
            return new EloquentSettingRepository(new Setting());
        });

        $this->app->bind(ImageRepository::class, function () {
            return new EloquentImageRepository(new Image());
        });

        $this->app->bind(LocationRepository::class, function () {
            return new EloquentLocationRepository(new Location());
        });

        $this->app->bind(PlanRepository::class, function () {
            return new EloquentPlanRepository(new Plan());
        });

        $this->app->bind(RightLeftSwipeRepository::class, function () {
            return new EloquentRightLeftRepository(new RightLeftSwipe());
        });

        $this->app->bind(SwipeMatchRepository::class, function () {
            return new EloquentSwipeMatchRepository(new SwipeMatch());
        });

        $this->app->bind(ChatMessageRepository::class, function () {
            return new EloquentChatMessageRepository(new ChatMessage());
        });

        $this->app->bind(ChatMessageRecipientRepository::class, function () {
            return new EloquentChatMessageRecipientRepository(new ChatMessageRecipient());
        });

        $this->app->bind(GoogleInAppSubscriptionRepository::class, function () {
            return new EloquentGoogleInAppSubscriptionRepository(new GoogleInAppSubscription());
        });

        $this->app->bind(AppleInAppSubscriptionRepository::class, function () {
            return new EloquentAppleInAppSubscriptionRepository(new AppleInAppSubscription());
        });

        $this->app->bind(ReportUserRepository::class, function () {
            return new EloquentReportUserRepository(new ReportUser());
        });

        $this->app->bind(VideoRepository::class, function () {
            return new EloquentVideoRepository(new Video());
        });

        $this->app->bind(SoundRepository::class, function () {
            return new EloquentSoundRepository(new Sound());
        });

        $this->app->bind(BoostProfileRepository::class, function () {
            return new EloquentBoostProfileRepository(new BoostProfile());
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            UserRepository::class,
            EmailVerificationRepository::class,
            SettingRepository::class,
            ImageRepository::class,
            LocationRepository::class,
            PlanRepository::class,
            RightLeftSwipeRepository::class,
            VideoRepository::class,
            SoundRepository::class,
            BoostProfileRepository::class
        ];
    }
}