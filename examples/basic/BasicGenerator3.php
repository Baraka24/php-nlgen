<?php

/*
 * Copyright (c) 2011-2022 Pablo Ariel Duboue <pablo.duboue@gmail.com>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining 
 * a copy of this software and associated documentation files (the "Software"), 
 * to deal in the Software without restriction, including without limitation 
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included 
 * in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER 
 * DEALINGS IN THE SOFTWARE.
 * 
 */

require __DIR__ . '/vendor/autoload.php';

use NLGen\Generator;

class BasicGenerator3 extends Generator {

  function top($data){
    return
      ucfirst($this->person($data[0]). " " .
	      $this->action($data[1], $data[2]). " " .
	      $this->item($data[3]));
  }

  protected function person($agent){
    return 
      ($this->onto->find_by_path(array($agent, "class", "requires_determiner")) == "yes"?"the ":"") . 
      $this->lex->string_for_id($agent);
  }
  
  protected function action($event, $action){
    return $this->lex->string_for_id($event)." ".$this->lex->string_for_id($action);
  }
  
  protected function item($theme){
    return
     ($this->onto->find_by_path(array($theme, "class", "requires_determiner")) == "yes"?"the ":"") .
      $this->lex->string_for_id($theme);
  }
}

global $argv,$argc;

$lexicon_json = <<<HERE
{
  "juan" :        {"string" : "Juan Perez"},
  "pedro" :       {"string" : "Pedro Gomez"},
  "helpdesk" :    {"string" : "helpdesk operator"},
  "start" :       {"string" : "started"},
  "ongoing" :     {"string" : "is"},
  "finish":       {"string" : "finished"},
  "general" :     {"string" : "working on"},
  "code" :        [ {"string" : "coding"}, {"string":"doing programming on" } ],
  "qa" :          {"string" : "doing QA on"},
  "comp_abc" :    {"string" : "Component ABC"},
  "itm_25" :      {"string" : "Item 25"},
  "sub_delivery": {"string" : "delivery subsystem"}
}
HERE
;

$onto_json = <<<HERE
{
  "juan" :        {"class" : "person"},
  "pedro" :       {"class" : "person"},
  "helpdesk" :    {"class" : "role"},
  "comp_abc" :    {"class" : "component"},
  "itm_25" :      {"class" : "item"},
  "sub_delivery": {"class" : "subsystem"},
  "person" :      { "requires_determiner":  "no" },
  "role" :        { "requires_determiner":  "yes" },
  "component" :   { "requires_determiner":  "no" },
  "item" :        { "requires_determiner":  "no" },
  "subsystem" :   { "requires_determiner":  "yes" }
}
HERE
;

global $argv,$argc;

// execute as php BasicGenerator3.php juan ongoing code sub_delivery
//                                    helpdesk finish qa itm_25

if($argc > 1) {
    $gen = BasicGenerator3::NewSealed($onto_json,$lexicon_json);
    $context = [];
    if($argc > 5) {
        $context['debug'] = true;
    }
    print $gen->generate(array_splice($argv,1), $context)."\n";

    print_r($gen->semantics());
}else{
    echo BasicGenerator3::Compile()[0];
}

