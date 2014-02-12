<?php namespace Werkzeugh\AngularTranslation;

use  BaseController,Redirect, View, Input, Response;
use  Illuminate\Support\Facades\DB;


class AngularTranslationController extends BaseController {




  public function __construct(/*LanguageProvider $languageProvider, LanguageEntryProvider $languageEntryProvider*/)
  {
    $this->setProviders(\App::make('Werkzeugh\TranslationAdmin\LanguageProvider'), \App::make('Werkzeugh\TranslationAdmin\LanguageEntryProvider'));
  }

  protected $languageProvider;
  protected $languageEntryProvider;

   protected function setProviders($languageProvider, $languageEntryProvider)
  {
    $this->languageProvider       = $languageProvider;
    $this->languageEntryProvider  = $languageEntryProvider;
  }

  public function getAvailableLanguages()
  {
    static $langs;
    if(!$langs)
    {
      foreach ($this->languageProvider->findAll() as $value) {
        $langs[$value->locale]=$value->getAttributes();
      }
    }
    return $langs;
  }

  public function getJson()
  {

      $model = $this->languageEntryProvider->createModel();


      $langid=$this->getAvailableLanguages()[Input::get('lang')]['id'];

      $query=DB::table($model->getTable());
      $query->whereLanguageId($langid);

      // $GLOBALS['debugsql']=1;


        DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);

      $res=$query->get(['namespace','group','item','text']);

      foreach ($res as $row) {
        if($row['namespace']=="*")
          $ret[$row['group']][$row['item']]=$row['text'];
        else
          $ret[$row['namespace']][$row['group']][$row['item']]=$row['text'];
      }


      return Response::json($ret);

  }
}
