<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of standard checkout HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Standard_Default
	extends Client_Html_Abstract
{
	/** client/html/checkout/standard/default/subparts
	 * List of HTML sub-clients rendered within the checkout standard section
	 *
	 * The output of the frontend is composed of the code generated by the HTML
	 * clients. Each HTML client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain HTML clients themselves and therefore a
	 * hierarchical tree of HTML clients is composed. Each HTML client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the HTML code generated by the parent is printed, then
	 * the HTML code of its sub-clients. The order of the HTML sub-clients
	 * determines the order of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the order of the output by reordering the subparts:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural HTML, the layout defined via CSS
	 * should support adding, removing or reordering content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2014.03
	 * @category Developer
	 */
	private $_subPartPath = 'client/html/checkout/standard/default/subparts';

	/** client/html/checkout/standard/address/name
	 * Name of the address part used by the checkout standard client implementation
	 *
	 * Use "Myname" if your class is named "Client_Html_Checkout_Standard_Address_Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2014.03
	 * @category Developer
	 */

	/** client/html/checkout/standard/delivery/name
	 * Name of the delivery part used by the checkout standard client implementation
	 *
	 * Use "Myname" if your class is named "Client_Html_Checkout_Standard_Delivery_Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2014.03
	 * @category Developer
	 */

	/** client/html/checkout/standard/payment/name
	 * Name of the payment part used by the checkout standard client implementation
	 *
	 * Use "Myname" if your class is named "Client_Html_Checkout_Standard_Payment_Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2014.03
	 * @category Developer
	 */

	/** client/html/checkout/standard/summary/name
	 * Name of the summary part used by the checkout standard client implementation
	 *
	 * Use "Myname" if your class is named "Client_Html_Checkout_Standard_Summary_Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2014.03
	 * @category Developer
	 */

	/** client/html/checkout/standard/order/name
	 * Name of the order part used by the checkout standard client implementation
	 *
	 * Use "Myname" if your class is named "Client_Html_Checkout_Standard_Order_Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2014.03
	 * @category Developer
	 */
	private $_subPartNames = array( 'address', 'delivery', 'payment', 'summary', 'order' );
	private $_cache;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string HTML code
	 */
	public function getBody( $uid = '', array &$tags = array(), &$expire = null )
	{
		$context = $this->_getContext();
		$view = $this->getView();

		try
		{
			$view = $this->_setViewParams( $view, $tags, $expire );

			$html = '';
			foreach( $this->_getSubClients() as $subclient ) {
				$html .= $subclient->setView( $view )->getBody( $uid, $tags, $expire );
			}
			$view->standardBody = $html;
		}
		catch( Client_Html_Exception $e )
		{
			$error = array( $this->_getContext()->getI18n()->dt( 'client/html', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( Controller_Frontend_Exception $e )
		{
			$error = array( $this->_getContext()->getI18n()->dt( 'controller/frontend', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( MShop_Exception $e )
		{
			$error = array( $this->_getContext()->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( Exception $e )
		{
			$context->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );

			$error = array( $context->getI18n()->dt( 'client/html', 'A non-recoverable error occured' ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}

		/** client/html/checkout/standard/default/template-body
		 * Relative path to the HTML body template of the checkout standard client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the layouts directory (usually in client/html/layouts).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page body
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/standard/default/template-header
		 */
		$tplconf = 'client/html/checkout/standard/default/template-body';
		$default = 'checkout/standard/body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string String including HTML tags for the header
	 */
	public function getHeader( $uid = '', array &$tags = array(), &$expire = null )
	{
		try
		{
			$view = $this->_setViewParams( $this->getView(), $tags, $expire );

			$html = '';
			foreach( $this->_getSubClients() as $subclient ) {
				$html .= $subclient->setView( $view )->getHeader( $uid, $tags, $expire );
			}
			$view->standardHeader = $html;

			/** client/html/checkout/standard/default/template-header
			 * Relative path to the HTML header template of the checkout standard client.
			 *
			 * The template file contains the HTML code and processing instructions
			 * to generate the HTML code that is inserted into the HTML page header
			 * of the rendered page in the frontend. The configuration string is the
			 * path to the template file relative to the layouts directory (usually
			 * in client/html/layouts).
			 *
			 * You can overwrite the template file configuration in extensions and
			 * provide alternative templates. These alternative templates should be
			 * named like the default one but with the string "default" replaced by
			 * an unique name. You may use the name of your project for this. If
			 * you've implemented an alternative client class as well, "default"
			 * should be replaced by the name of the new class.
			 *
			 * @param string Relative path to the template creating code for the HTML page head
			 * @since 2014.03
			 * @category Developer
			 * @see client/html/checkout/standard/default/template-body
			 */
			$tplconf = 'client/html/checkout/standard/default/template-header';
			$default = 'checkout/standard/header-default.html';

			return $view->render( $this->_getTemplate( $tplconf, $default ) );
		}
		catch( Exception $e )
		{
			$this->_getContext()->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );
		}
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Interface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		return $this->_createSubClient( 'checkout/standard/' . $type, $name );
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$view = $this->getView();
		$context = $this->_getContext();

		try
		{
			parent::process();
		}
		catch( Client_Html_Exception $e )
		{
			$error = array( $this->_getContext()->getI18n()->dt( 'client/html', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( Controller_Frontend_Exception $e )
		{
			$error = array( $this->_getContext()->getI18n()->dt( 'controller/frontend', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( MShop_Plugin_Provider_Exception $e )
		{
			$errors = array( $this->_getContext()->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$errors = array_merge( $errors, $this->_translatePluginErrorCodes( $e->getErrorCodes() ) );

			$view->summaryErrorCodes = $e->getErrorCodes();
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $errors;
		}
		catch( MShop_Exception $e )
		{
			$error = array( $this->_getContext()->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( Exception $e )
		{
			$context->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );

			$error = array( $context->getI18n()->dt( 'client/html', 'A non-recoverable error occured' ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function _getSubClientNames()
	{
		return $this->_getContext()->getConfig()->get( $this->_subPartPath, $this->_subPartNames );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return MW_View_Interface Modified view object
	 */
	protected function _setViewParams( MW_View_Interface $view, array &$tags = array(), &$expire = null )
	{
		if( !isset( $this->_cache ) )
		{
			$context = $this->_getContext();

			$basketCntl = Controller_Frontend_Factory::createController( $context, 'basket' );
			$view->standardBasket = $basketCntl->get();

			$basketTarget = $view->config( 'client/html/basket/standard/url/target' );
			$basketController = $view->config( 'client/html/basket/standard/url/controller', 'basket' );
			$basketAction = $view->config( 'client/html/basket/standard/url/action', 'index' );
			$basketConfig = $view->config( 'client/html/basket/standard/url/config', array() );


			/** client/html/checkout/standard/url/target
			 * Destination of the URL where the controller specified in the URL is known
			 *
			 * The destination can be a page ID like in a content management system or the
			 * module of a software development framework. This "target" must contain or know
			 * the controller that should be called by the generated URL.
			 *
			 * @param string Destination of the URL
			 * @since 2014.03
			 * @category Developer
			 * @see client/html/checkout/standard/url/controller
			 * @see client/html/checkout/standard/url/action
			 * @see client/html/checkout/standard/url/config
			 */
			$checkoutTarget = $view->config( 'client/html/checkout/standard/url/target' );

			/** client/html/checkout/standard/url/controller
			 * Name of the controller whose action should be called
			 *
			 * In Model-View-Controller (MVC) applications, the controller contains the methods
			 * that create parts of the output displayed in the generated HTML page. Controller
			 * names are usually alpha-numeric.
			 *
			 * @param string Name of the controller
			 * @since 2014.03
			 * @category Developer
			 * @see client/html/checkout/standard/url/target
			 * @see client/html/checkout/standard/url/action
			 * @see client/html/checkout/standard/url/config
			 */
			$checkoutController = $view->config( 'client/html/checkout/standard/url/controller', 'checkout' );

			/** client/html/checkout/standard/url/action
			 * Name of the action that should create the output
			 *
			 * In Model-View-Controller (MVC) applications, actions are the methods of a
			 * controller that create parts of the output displayed in the generated HTML page.
			 * Action names are usually alpha-numeric.
			 *
			 * @param string Name of the action
			 * @since 2014.03
			 * @category Developer
			 * @see client/html/checkout/standard/url/target
			 * @see client/html/checkout/standard/url/controller
			 * @see client/html/checkout/standard/url/config
			 */
			$checkoutAction = $view->config( 'client/html/checkout/standard/url/action', 'index' );

			/** client/html/checkout/standard/url/config
			 * Associative list of configuration options used for generating the URL
			 *
			 * You can specify additional options as key/value pairs used when generating
			 * the URLs, like
			 *
			 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
			 *
			 * The available key/value pairs depend on the application that embeds the e-commerce
			 * framework. This is because the infrastructure of the application is used for
			 * generating the URLs. The full list of available config options is referenced
			 * in the "see also" section of this page.
			 *
			 * @param string Associative list of configuration options
			 * @since 2014.03
			 * @category Developer
			 * @see client/html/checkout/standard/url/target
			 * @see client/html/checkout/standard/url/controller
			 * @see client/html/checkout/standard/url/action
			 * @see client/html/url/config
			 */
			$checkoutConfig = $view->config( 'client/html/checkout/standard/url/config', array() );


			$steps = (array) $context->getConfig()->get( $this->_subPartPath, $this->_subPartNames );
			$view->standardSteps = $steps;

			/** client/html/checkout/standard/url/step-active
			 * Name of the checkout process step to jump to if no previous step requires attention
			 *
			 * The checkout process consists of several steps which are usually
			 * displayed one by another to the customer. If the data of a step
			 * is already available, then that step is skipped. The active step
			 * is the one that is displayed if all other steps are skipped.
			 *
			 * If one of the previous steps misses some data the customer has
			 * to enter, then this step is displayed first. After providing
			 * the missing data, the whole series of steps are tested again
			 * and if no other step requests attention, the configured active
			 * step will be displayed.
			 *
			 * The order of the steps is determined by the order of sub-parts
			 * that are configured for the checkout client.
			 *
			 * @param string Name of the confirm standard HTML client
			 * @since 2014.07
			 * @category Developer
			 * @category User
			 * @see client/html/checkout/standard/default/subparts
			 */
			$default = $view->config( 'client/html/checkout/standard/url/step-active', 'summary' );
			$default = ( !in_array( $default, $steps ) ? reset( $steps ) : $default );

			$current = $view->param( 'c-step', $default );
			$cpos = $cpos = array_search( $current, $steps );

			if( !isset( $view->standardStepActive )
				|| ( ( $apos = array_search( $view->standardStepActive, $steps ) ) !== false
				&& $cpos !== false && $cpos < $apos )
			) {
				$view->standardStepActive = $current;
			}

			$activeStep = $view->standardStepActive;


			$step = null;
			do {
				$lastStep = $step;
			}
			while( ( $step = array_shift( $steps ) ) !== null && $step !== $activeStep );


			if( $lastStep !== null ) {
				$view->standardUrlBack = $view->url( $checkoutTarget, $checkoutController, $checkoutAction, array( 'c-step' => $lastStep ), array(), $checkoutConfig );
			} else {
				$view->standardUrlBack = $view->url( $basketTarget, $basketController, $basketAction, array(), array(), $basketConfig );
			}

			if( ( $nextStep = array_shift( $steps ) ) !== null ) {
				$param = ( $activeStep === $default ? array( 'c-step' => $nextStep ) : array() );
				$view->standardUrlNext = $view->url( $checkoutTarget, $checkoutController, $checkoutAction, $param, array(), $checkoutConfig );
			} else {
				$view->standardUrlNext = '';
			}


			$this->_cache = $view;
		}

		return $this->_cache;
	}
}