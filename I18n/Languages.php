<?php
class Languages extends  L10n {
    public function getLanguageList() {
        return array('en'=>'English', 'he'=>'Hebrew');
        $languaqges = array();
        foreach($this->_l10nMap AS $langKey) {
            $languaqges[$langKey] = $this->_l10nCatalog[$langKey]['language'];
        }

        return $languaqges;
    }
}
?>