<?php namespace Werkzeugh\AngularTranslation;

use  BaseController,Redirect, View, Input, Response;
use L_DB as DB;


class AngularTranslationController extends BaseController {



  public function getJson()
  {


    return '
    {
      "HEADLINE": "What an awesome module!",
      "PARAGRAPH": "Srsly, ➜ [{{username}}]",
      "NAMESPACE": {
        "PARAGRAPH": "And it comes with awesome features!"
      }
    }
    ';



  }
}
