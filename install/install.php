<?php
/**
* @package   jGravatar
* @author    Florian Lonqueu-Brochard
* @licence MIT
*/


class jGravatarModuleInstaller extends jInstallerModule {

    
    public function setEntryPoint($ep, $config, $dbProfile) {
          parent::setEntryPoint($ep, $config, $dbProfile);
          return md5($ep->configFile.'-'.$this->version);
    }

    function install() {

          $conf = $this->config->getMaster();

          $conf->setValue('jGravatar.access', '1', 'modules');

          //$conf->setValue('; relative to the www path', '', 'jGravatar');
          $conf->setValue('cache_dir', 'uploads/jGravatar/', 'jGravatar');
          $conf->setValue('expire_ago', '3 days', 'jGravatar');
          $conf->setValue('default_image', 'gravatar_default.png', 'jGravatar');
          $conf->save();
    }
}