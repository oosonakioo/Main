<?php

namespace Mews\Captcha;

use Laravel\Lumen\Routing\Controller;

/**
 * Class CaptchaController
 */
class LumenCaptchaController extends Controller
{
    /**
     * get CAPTCHA
     *
     * @param  string  $config
     * @return \Intervention\Image\ImageManager->response
     */
    public function getCaptcha(Captcha $captcha, $config = 'default')
    {
        return $captcha->create($config);
    }
}
