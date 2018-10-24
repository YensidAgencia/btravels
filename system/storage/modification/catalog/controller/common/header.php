<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}


        if ( $this->config->get( 'pavoblog_default_style' ) ){
            $file = DIR_TEMPLATE . 'default/stylesheet/pavoblog.min.css';
            if ( file_exists( $file ) ) {
                $file = str_replace( DIR_APPLICATION, basename( DIR_APPLICATION ) . '/', $file );
                $this->document->addStyle( $file );
            }
        }

               $data['quickview'] = $this->load->controller('extension/module/pavquickview');
            


            $this->document->addStyle( 'catalog/view/javascript/pavobuilder/dist/pavobuilder.min.css' );
            $this->document->addScript( 'catalog/view/javascript/pavobuilder/dist/pavobuilder.min.js' );
        

            $data['pavothemer'] = $this->load->controller('extension/module/pavothemer');
            $this->load->model( 'extension/module/pavothemer' );
            $data['pavmegamenu'] = $this->load->controller('extension/module/pavmegamenu');
            $data['pavverticalmegamenu'] = $this->load->controller('extension/module/pavverticalmenu');
            $data['pavoheader'] = $this->model_extension_module_pavothemer->getHeader();
            $data['offcanvas']  = $this->model_extension_module_pavothemer->getConfig( 'offcanvas' );
       
		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		
		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');


            if( empty($data['pavoheader']) ) {    
                $header_layout  = $this->model_extension_module_pavothemer->getConfig( 'header_layout' );
                if( $header_layout ){
                    return $this->load->view('common/'.trim($header_layout), $data);
                }
            }

       
		
        $data['body_class'] = $this->getBodyClass();
        // pavothemer header layout file
        $data['styles'] = $this->model_extension_module_pavothemer->compressCss( $data['styles'] );
        $data['scripts'] = $this->model_extension_module_pavothemer->compressJs( $data['scripts'] );

        return $this->load->view('common/header', $data);
    }

    /**
     * Get body class
     */
    public function getBodyClass() {
        $this->load->model( 'extension/module/pavothemer' );
        $route = ! empty( $this->request->get['route'] ) ? $this->request->get['route'] : 'common/home';

        $class = '';
        if( $this->config->get( 'pavothemer_keepheader' ) ){
            $class = ' has-header-sticky';
        }
        $class .= $this->model_extension_module_pavothemer->getBodyClass();
        return 'page-' . implode( '-', explode( '/', $route ) ) . $class;
            
	}
}