<?php
class TemplateProductListInputRender extends modTemplateVarInputRender {
    public function getTemplate() {
        $path = 'components/sead/';
	    $corePath = $this->modx->getOption('sead.core_path', null, $this->modx->getOption('core_path') . $path);
        return $corePath . 'elements/tv/tpl/productlist.html';    
    }
    public function process($value,array $params = array()) {

        $namespace = 'sead';
        $this->modx->lexicon->load('tv_widget', $namespace . ':manager');
		
	//$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.combo.js');
	//$modx->regClientStartupScript($modx->getOption('site_url').'assets/components/sead/modext/sead.rate-list-grid.js');        
        
        //$properties = isset($params['columns']) ? $params : $this->getProperties();
        $properties = $params; 		
    }
}
return 'TemplateProductListInputRender';
