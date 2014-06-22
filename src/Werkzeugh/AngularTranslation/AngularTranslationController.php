<?php namespace Werkzeugh\AngularTranslation;

use  BaseController,Redirect, View, Input, Response;
use  Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class AngularTranslationController extends \BaseControllerForPackages {


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

  public function getMissing()
  {


//stop session race conditions:
    Config::set('session.driver','array');

    $ret['status']='error';
    $key=Input::get('key');
    $lang=Input::get('lang');
    if($lang && $key)
    {
      $trans=\Lang::get($key);

      $ret['info']="missing $key ($lang)";

      if($trans && $trans!=$key)
      {
        $ret['info'].="adding translation";
        $ret['trans']=$trans;
        $ret=array_replace_recursive($ret,$this->addValueToTranslationDatabase($key,$lang,$trans));
      }
    }

    return Response::json($ret);
  }

function getPartsForKey($key)
{
  $ret=[];

  $parts=explode('.',$key);

  if(sizeof($parts)>0)
    $ret['item']=trim(array_pop($parts));
  if(sizeof($parts)>0)
    $ret['group']=trim(array_pop($parts));
  if(sizeof($parts)>0)
    $ret['namespace']=trim(array_pop($parts));

  if(!$ret['namespace'])
    $ret['namespace']='*';

  if($ret['group'])
    return $ret;
  else
    return NULL;

}

function addValueToTranslationDatabase($key,$lang,$default_text)
{

  $ret=[];
  if($keyparts=$this->getPartsForKey($key))
  {
    $ret['keyparts']=$keyparts;
    $langid=$this->getAvailableLanguages()[$lang]['id'];

    $newEntry=Array(
      'language_id'=>$langid,
      'namespace'=>$keyparts['namespace'],
      'group'=>$keyparts['group'],
      'item'=>$keyparts['item'],
      'text'=>$default_text,
      'locked'=>false,
      );

    try
    {
      $this->languageEntryProvider->create($newEntry);
      $ret['status']=$ok;
    } catch(\Illuminate\Database\QueryException $e){
      $ret['status']='error';
      $ret['msg']=$e->getMessage();

      return $ret;
    }

  }

  return $ret;


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
