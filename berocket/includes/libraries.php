<?php
if ( ! class_exists( 'Ramphor_framework_libraries' ) ) {
	class Ramphor_framework_libraries {
		public $libraries_name = array(
			'addons'    => 'addons/addons.php',
			'templates' => 'templates/templates.php',
			'popup'     => 'popup.php',
			'tooltip'   => 'tippy.php',
			'tippy'     => 'tippy.php',
			'feature'   => 'feature_tab.php',
		);
		public $info, $values, $options;
		public $libraries_class = array();
		function __construct( $libraries, $info, $values, $options ) {
			$this->info    = $info;
			$this->values  = $values;
			$this->options = $options;
			foreach ( $libraries as $library ) {
				$library_file = ( isset( $this->libraries_name[ $library ] ) ? $this->libraries_name[ $library ] : $library );
				if ( file_exists( Ramphor_framework_dir . '/libraries/' . $library_file ) ) {
					include_once Ramphor_framework_dir . '/libraries/' . $library_file;
					if ( method_exists( $this, $library ) ) {
						$this->libraries_class[ $library ] = $this->$library();
					}
				}
			}
		}
		function addons() {
			return new Ramphor_framework_addons( $this->info, $this->values, $this->options );
		}
		function templates() {
			return new Ramphor_framework_templates( $this->info, $this->values, $this->options );
		}
		function feature() {
			return new Ramphor_framework_feature_tab( $this->info, $this->values, $this->options );
		}
	}
}
