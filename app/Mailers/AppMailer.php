<?php

namespace App\Mailers;

use App\User;
use Illuminate\Contracts\Mail\Mailer;

class AppMailer
{

    /**
     * The Laravel Mailer instance.
     *
     * @var Mailer
     */
    protected $mailer;

    /**
     * The sender of the email.
     *
     * @var string
     */
    protected $from = 'no-reply@vinviter.com';

    /**
     * The recipient of the email.
     *
     * @var string
     */
    protected $to;

    /**
     * The view for the email.
     *
     * @var string
     */
    protected $view;

    /**
     * The data associated with the view for the email.
     *
     * @var array
     */
    protected $data = [];

    /* The subject for the email */
    protected $subject;


    /**
     * Create a new app mailer instance.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Deliver the email confirmation.
     *
     * @param  User $user
     * @return void
     */
    public function sendEmailConfirmationTo($user)
    {
        $this->to      = $user->email;
        $this->view    = 'emails.confirm';
        $this->data    = compact('user');
        $this->subject = 'Sign Up Confirmation';

        $this->deliver();
    }

    /**
     * Deliver the contact form message to administrator.
     *
     * @return void
     */
    public function sendContactFormMessage($data)
    {
        $this->to      = config('common.administrator_email');
        $this->view    = 'emails.contactForm';
        $this->data    = compact('data');
        $this->subject = 'Contact form message';

        $this->deliver();
    }

    /**
     * Deliver the email.
     *
     * @return void
     */
    public function deliver()
    {
        $this->mailer->send($this->view, $this->data, function ($message) {
            $message->from($this->from, 'Vinviter')
                    ->to($this->to)->subject($this->subject);
        });
    }
}
