<?php

namespace Crm\UsersModule\Presenters;

use Crm\ApplicationModule\Presenters\FrontendPresenter;
use Crm\UsersModule\Auth\Sso\GoogleSignIn;
use Crm\UsersModule\Auth\Sso\SsoException;
use Crm\UsersModule\Auth\Sso\SsoRedirectValidator;
use Tracy\Debugger;

class GooglePresenter extends FrontendPresenter
{
    private const SESSION_SECTION = 'google_presenter';

    private $googleSignIn;

    private $ssoRedirectValidator;

    public function __construct(
        GoogleSignIn $googleSignIn,
        SsoRedirectValidator $ssoRedirectValidator
    ) {
        parent::__construct();
        $this->googleSignIn = $googleSignIn;
        $this->ssoRedirectValidator = $ssoRedirectValidator;
    }

    public function renderSign()
    {
        if (!$this->googleSignIn->isEnabled()) {
            $this->redirect('Sign:in');
        }

        $session = $this->getSession(self::SESSION_SECTION);
        unset($session->finalUrl);

        // Final URL destination
        $finalUrl = $this->getParameter('url');
        if ($finalUrl) {
            $refererUrl = $this->getHttpRequest()->getReferer();

            if ($this->ssoRedirectValidator->isAllowed($finalUrl)) {
                $session->finalUrl = $finalUrl;
            } elseif ($refererUrl && $this->ssoRedirectValidator->isAllowed($refererUrl->getAbsoluteUrl())) {
                // Redirect backup to Referer (if provided 'url' parameter is invalid or manipulated)
                $session->finalUrl = $refererUrl->getAbsoluteUrl();
            }
        }

        try {
            // redirect URL is your.crm.url/users/google/callback
            $authUrl = $this->googleSignIn->signInRedirect($this->link('//callback'));
            // redirect user to google authentication
            $this->redirectUrl($authUrl);
        } catch (SsoException $e) {
            Debugger::log($e->getMessage(), Debugger::WARNING);
            $this->flashMessage($this->translator->translate('users.frontend.google.fail'));
            $this->redirect('Sign:in');
        }
    }

    public function renderCallback()
    {
        if (!$this->googleSignIn->isEnabled()) {
            $this->redirect('Sign:in');
        }

        try {
            $user = $this->googleSignIn->signInCallback($this->link('//callback'));

            // AutoLogin will log in user - create access token and set user flag (in session) to authenticated
            $this->getUser()->login([
                'user' => $user,
                'autoLogin' => true,
                'source' => GoogleSignIn::ACCESS_TOKEN_SOURCE_WEB_GOOGLE_SSO,
            ]);
        } catch (SsoException $e) {
            Debugger::log($e, Debugger::WARNING);
            $this->flashMessage($this->translator->translate('users.frontend.google.fail'));
            $this->redirect('Sign:in');
        }

        $session = $this->getSession(self::SESSION_SECTION);

        $finalUrl = $session->finalUrl;
        if ($finalUrl) {
            $this->redirectUrl($finalUrl);
        } else {
            $this->redirect($this->homeRoute);
        }
    }
}
