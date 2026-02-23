<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // This will disable SSL verification for SMTP connections
        // ONLY USE IN DEVELOPMENT!
        if (config('mail.default') === 'smtp') {
            $this->app->resolving(\Symfony\Component\Mailer\Transport\TransportInterface::class, function ($transport) {
                if (method_exists($transport, 'getStream')) {
                    $stream = $transport->getStream();
                    if ($stream instanceof SocketStream) {
                        $stream->setStreamOptions([
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true,
                            ],
                        ]);
                    }
                }
                return $transport;
            });
        }
    }
}
