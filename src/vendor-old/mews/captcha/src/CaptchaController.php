<?php

namespace Mews\Captcha;

use Illuminate\Routing\Controller;

/**
 * Class CaptchaController
 */
class CaptchaController extends Controller
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
