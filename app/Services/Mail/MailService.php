<?php

namespace App\Services\Mail;

use App\Claim;
use App\Debt;
use App\Mail\DefaultMail;
use App\Payment;
use App\Services\Service;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService extends Service
{
  /**
   * send email to user
   *
   * @param $email
   * @param $subject
   * @param $body
   * @param $name
   * @param null $file
   * @param string $fileType
   * @return bool
   */
  public function sendMail($email,$subject,$body,$name,$file = null,$fileName = 'warranty.pdf',$fileType = 'letter')
  {
    try {
      Mail::to($email)->cc(['c.braber@ceesbraber.nl'])->later(now()->addMinutes(env('SEND_MAIL_AFTER_MINS')), new DefaultMail([
        'subject' => $subject,
        'name' => $name,
        'content' => $body,
        'file' => $file,
        'fileType' => $fileType,
        'fileName' => $fileName
      ]));
      return true;
    }catch (\Exception $error){
      Log::info('Email not sent: '.$error->getMessage());
    }

    return false;
  }
}
