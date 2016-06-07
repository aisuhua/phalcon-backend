<?php
namespace Backend\Mvc\Controllers;

trait TokenTrait
{
    protected function checkTokenPost()
    {
        if (!$this->security->checkToken()) { 
            $this->flashSession->error('Token error. This might be CSRF attack.');
            return false;
        }

        return true;
    }

    protected function checkTokenGetJson()
    {
        $csrfKey = $this->session->get('$PHALCON/CSRF/KEY$');

        return $this->security->checkToken(
            $csrfKey,
            $this->request->getQuery($csrfKey, null, 'dummy')
        );
    }

    protected function checkTokenGet()
    {
        $csrfKey = $this->session->get('$PHALCON/CSRF/KEY$');
        $csrfToken = $this->request->getQuery($csrfKey, null, 'dummy');

        if (!$this->security->checkToken($csrfKey, $csrfToken)) {
            $this->flashSession->error('Token error. This might be CSRF attack.');
            return false;
        }
        return true;
    }
}
