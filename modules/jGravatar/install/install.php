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

        $access = $conf->getValue('jGravatar.access', 'module');
        
        if ($access == null)
            $conf->setValue('jGravatar.access', '1', 'modules');
        elseif ($access != 1){
            $conf->removeValue('jGravatar.access', 'modules');
            $conf->setValue('jGravatar.access', '1', 'modules');
        }

        //cache_dir parameter
        if ($conf->getValue('cache_dir', 'jGravatar') == null){
            $conf->setValue('; relative to the www path', '', 'jGravatar');
            $conf->setValue('cache_dir', 'uploads/jGravatar/', 'jGravatar');
        }
        
        //expire_ago parameter
        if ($conf->getValue('expire_ago', 'jGravatar') == null)
            $conf->setValue('expire_ago', '3 days', 'jGravatar');
        
        //default_image parameter
        if ($conf->getValue('default_image', 'jGravatar') == null)
            $conf->setValue('default_image', 'gravatar_default.png', 'jGravatar');
        
        $conf->save();
    }
}