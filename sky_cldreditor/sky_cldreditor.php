<?php
/*
* Edited by wert.tw
*/

if (!defined('_PS_VERSION_'))
	exit;
class sky_cldreditor extends Module
{	
	public $msgHtml = '';
    public $path = _PS_TRANSLATIONS_DIR_.'cldr/';
    public $currencies = array('TWD','CNY','JPY','USD','KRW','HKD','GBP','EUR');
	public function __construct()
	{
        $this->bootstrap = true;
		$this->name = 'sky_cldreditor';
		$this->version = '0.5.0';
		$this->author = 'SKY';
        $this->displayName = $this->trans('cldr 編輯模組', array(), 'Modules.Cldr_editor.Admin');
        $this->description = $this->trans('可以編輯cldr內的檔案.', array(), 'Modules.Cldr_editor.Admin');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
        $this->confirmUninstall = $this->trans('確定移除這個模組?', array(), 'Modules.Cldr_editor.Admin');
        $this->templateFile = 'module:sky_cldr_editor/views/templates/admin/cldreditor.tpl';
		
		parent::__construct();
		
	}
	public function getContent()
	{
		if(Tools::isSubmit('cldrForm_submit')){
			
				echo '<BR>';
				echo '<BR>';
				echo '<BR>';
				echo '<BR>';
				echo '<BR>';
				echo '<BR>';
				echo '<BR>';
				echo '<BR>';
				echo '<BR>';
			$name = Tools::getValue('name');
				$file = fopen($this->path.$name,"r");
				$content = fgets($file);
				$content = json_decode($content,true);
				fclose($file);
				reset($content);
				$lang_name = key($content['main']);
			if(Tools::getValue('type') == 'numbers'){				
				$file = fopen($this->path.$name,"r");
				$content = fgets($file);
				$content = json_decode($content,true);
				fclose($file);
				$content['main'][$lang_name]['numbers']['currencyFormats-numberSystem-hanidec']['accounting'] = Tools::getValue('hanidec-accounting');
				$content['main'][$lang_name]['numbers']['currencyFormats-numberSystem-hanidec']['standard'] = Tools::getValue('hanidec-standard');
				$content['main'][$lang_name]['numbers']['currencyFormats-numberSystem-latn']['accounting'] = Tools::getValue('latn-accounting');
				$content['main'][$lang_name]['numbers']['currencyFormats-numberSystem-latn']['standard'] = Tools::getValue('latn-standard');					
				
				$content = json_encode($content);
				$file = fopen($this->path.$name,"w");
				fwrite($file, $content);
				fclose($file);
			}
			else if(Tools::getValue('type') == 'currencies'){
				
				foreach($this->currencies as $currency){
					if(!empty(Tools::getValue('displayName_'.$currency))){
						$content['main'][$lang_name]['numbers']['currencies'][$currency]['displayName'] = Tools::getValue('displayName_'.$currency);
					}
					if(!empty(Tools::getValue('displayName-count-other_'.$currency))){					
						$content['main'][$lang_name]['numbers']['currencies'][$currency]['displayName-count-other'] = Tools::getValue('displayName-count-other_'.$currency);
					}
					if(!empty(Tools::getValue('symbol_'.$currency))){
						$content['main'][$lang_name]['numbers']['currencies'][$currency]['symbol'] = Tools::getValue('symbol_'.$currency);
					}
					if(!empty(Tools::getValue('symbol-alt-narrow_'.$currency))){
						$content['main'][$lang_name]['numbers']['currencies'][$currency]['symbol-alt-narrow'] = Tools::getValue('symbol-alt-narrow_'.$currency);
					}
				}			
				$content = json_encode($content);
				$file = fopen($this->path.$name,"w");
				if($file){
					fwrite($file, $content);
					fclose($file);
				}
			}
		}
		if(Tools::isSubmit('updatesky_cldreditor')){
			
			return $this->msgHtml.$this->cldrForm();
			
		}
		else		
			return $this->msgHtml.$this->cldrList();
	}
	public function cldrList()
	{
		$cldrFiles = $this->getcldrFiles();
		
		$fields_list = array(
			'name' => array(
				'title' => $this->l('檔案名稱',$this->name),
				'type' => 'text',
				'class' => 'fixed-width-md',
				'orderby' => false,
				'search' => false
			),
			'path' => array(
				'title' => $this->l('路徑',$this->name),
				'type' => 'text',
				'class' => 'fixed-width-md',
				'orderby' => false,
				'search' => false
			)
		);
		
		/**	產生表單	**/
		//生成物件
		$helper = new HelperList();
        $helper->list_id = $this->name;
		$helper->table = $this->name;
        $helper->icon = 'icon-money';
		$helper->title = $this->l('cldr檔案列表',$this->name);
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->submit_action = 'cldrList_submit';
		$helper->no_link = false;
		$helper->simple_header = false;
		$helper->identifier = 'name';
		$helper->shopLinkType = '';
		$id_lang=(int)$this->context->language->id;
		$helper->listTotal = count($cldrFiles);
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$fields_value = array();
		$helper->tpl_vars = array(
			'fields_value' => $fields_value,
			'languages' => $this->context->controller->getLanguages()
		);

		return $helper->generateList($cldrFiles, $fields_list);	
	}
	public function cldrForm()
	{
		$name = Tools::getValue('name');
		$file = fopen($this->path.$name,"r");
		$content = fgets($file);
		$content = json_decode($content,true);
		fclose($file);
		reset($content);
		$lang_name = key($content['main']);
		
		if(isset($content['main'][$lang_name]['numbers']['currencyFormats-numberSystem-hanidec'])&&
		isset($content['main'][$lang_name]['numbers']['currencyFormats-numberSystem-hanidec'])){
			$input_fields = array(array(
										'type' => 'hidden',
										'name' => 'type',
										'class' => 'fixed-width-xl'
									),
									array(
										'type' => 'hidden',
										'name' => 'name',
										'class' => 'fixed-width-xl'
									),
									array(
										'type' => 'text',
										'label' => 'hanidec-accounting',
										'name' => 'hanidec-accounting',
										'class' => 'fixed-width-xl'
									),
									array(
										'type' => 'text',
										'label' => 'hanidec-standard',
										'name' => 'hanidec-standard',
										'class' => 'fixed-width-xl'
									),
									array(
										'type' => 'text',
										'label' => 'latn-accounting',
										'name' => 'latn-accounting',
										'class' => 'fixed-width-xl'
									),
									array(
										'type' => 'text',
										'label' => 'latn-standard',
										'name' => 'latn-standard',
										'class' => 'fixed-width-xl'
									));
									
			$fields_value = array('type' => 'numbers',
									'name' => Tools::getValue('name'),
									'hanidec-accounting' => $content['main'][$lang_name]['numbers']['currencyFormats-numberSystem-hanidec']['accounting'],
									'hanidec-standard' => $content['main'][$lang_name]['numbers']['currencyFormats-numberSystem-hanidec']['standard'],
									'latn-accounting' => $content['main'][$lang_name]['numbers']['currencyFormats-numberSystem-latn']['accounting'],
									'latn-standard' => $content['main'][$lang_name]['numbers']['currencyFormats-numberSystem-latn']['standard']);						
		}
		
		if(isset($content['main'][$lang_name]['numbers']['currencies'])){
			$input_fields = array();
			
			$field = array(
				'type' => 'hidden',
				'name' => 'type',
				'class' => 'fixed-width-xl'
			);
			array_push($input_fields,$field);
			$fields_value['type'] = 'currencies';
			
			$field = array(
				'type' => 'hidden',
				'name' => 'name',
				'class' => 'fixed-width-xl'
			);
			array_push($input_fields,$field);
			$fields_value['name'] = Tools::getValue('name');
			
			foreach($this->currencies as $currency){
				if(isset($content['main'][$lang_name]['numbers']['currencies'][$currency]['displayName'])){
					$field = array(
						'type' => 'text',
						'label' => 'displayName',
						'name' => 'displayName_'.$currency,
						'class' => 'fixed-width-xl'
					);
					array_push($input_fields,$field);
					$fields_value['displayName_'.$currency] = $content['main'][$lang_name]['numbers']['currencies'][$currency]['displayName'];
				}
				if(isset($content['main'][$lang_name]['numbers']['currencies'][$currency]['displayName-count-other'])){
					$field = array(
						'type' => 'text',
						'label' => 'displayName-count-other',
						'name' => 'displayName-count-other_'.$currency,
						'class' => 'fixed-width-xl'
					);
					array_push($input_fields,$field);
					$fields_value['displayName-count-other_'.$currency] = $content['main'][$lang_name]['numbers']['currencies'][$currency]['displayName-count-other'];
					
				}
				if(isset($content['main'][$lang_name]['numbers']['currencies'][$currency]['symbol'])){
					$field = array(
						'type' => 'text',
						'label' => 'symbol',
						'name' => 'symbol_'.$currency,
						'class' => 'fixed-width-xl'
					);
					array_push($input_fields,$field);
					$fields_value['symbol_'.$currency] = $content['main'][$lang_name]['numbers']['currencies'][$currency]['symbol'];
					
				}
				if(isset($content['main'][$lang_name]['numbers']['currencies'][$currency]['symbol-alt-narrow'])){
					$field = array(
						'type' => 'text',
						'label' => 'symbol-alt-narrow',
						'name' => 'symbol-alt-narrow_'.$currency,
						'class' => 'fixed-width-xl'
					);
					array_push($input_fields,$field);
					$fields_value['symbol-alt-narrow_'.$currency] = $content['main'][$lang_name]['numbers']['currencies'][$currency]['symbol-alt-narrow'];
				}
			
			}
		}
		
		$form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('cldr檔案內容',$this->name),
				),
				'input' => 
					$input_fields,
				'submit' => array(
					'title' => $this->l('儲存',$this->name),
				)
			)
		);
		$helper = new HelperForm();
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->shopLinkType = '';
		$helper->submit_action = 'cldrForm_submit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		
		
		$helper->tpl_vars = array(
			'fields_value' => $fields_value,
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		return $helper->generateForm(array($form));
	}
	public function install()
	{	
		return parent::install();
	}
	public function uninstall()
	{
		return parent::uninstall();
	}
	/************************************************************
	 *	儲存cldr檔案
	 *
	 *	$info			Array		忽略的檔案
	 *
	 *	return			Bool		是否成功
	 *************************************************************/
	public function savecldrFile($info)
	{

  
		return true;
	}
	/************************************************************
	 *	取得cldr料夾中中，所有檔案資訊
	 *
	 *	return			Array		cldr中的檔案
	 *************************************************************/
	public function getcldrFiles()
	{
		$ignore = array("ignore.txt");
		$files_tmp = glob($this->path.'*');
		$files = array();
		
		foreach($files_tmp as $num => $file){
			if(is_dir($file))
				continue;
			
			$name = substr($file, strrpos($file,'/')+1);
			
			if(strpos($name,'.'))
				continue;
			
			if(in_array($name,$ignore))
				continue;
			$info_tmp = array('path'=>$file, 'name'=>$name);
			array_push($files, $info_tmp);
			
		}
		
		return $files;
	}
} 