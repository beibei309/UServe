<?php

namespace App\Services;

use App\Mail\NewServiceRequestNotification;
use App\Models\ServiceRequest;
use App\Models\StudentService;
use App\Models\User;
use App\Notifications\NewServiceRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ServiceRequestNotificationService
{
    public function notifyCreated(ServiceRequest $serviceRequest, StudentService $studentService, User $requester): void
    {
        try {
            $studentService->user->notify(new NewServiceRequest($serviceRequest));

            if ($studentService->user->hu_email) {
                Mail::to($studentService->user->hu_email)
                    ->send(new NewServiceRequestNotification($serviceRequest, 'provider'));
            }

            if ($requester->hu_email) {
                Mail::to($requester->hu_email)
                    ->send(new NewServiceRequestNotification($serviceRequest, 'student'));
            }
        } catch (\Throwable $notifyError) {
            Log::warning('ServiceRequest notifications failed: '.$notifyError->getMessage(), [
                'service_request_id' => $serviceRequest->hsr_id,
                'requester_id' => $requester->hu_id,
                'provider_id' => $studentService->hss_user_id,
            ]);
        }
    }
}
