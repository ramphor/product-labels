<?php
if ( ! class_exists( 'Ramphor_conditions' ) ) {
	class Ramphor_conditions {
		public $conditions = array();
		public $option_name, $hook_name;
		public function __construct( $option_name, $hook_name, $conditions = array() ) {
			$conditions        = apply_filters( $hook_name . '_conditions_list', $conditions );
			$this->conditions  = $conditions;
			$this->option_name = $option_name;
			$this->hook_name   = $hook_name;
			$ready_conditions  = static::get_conditions();
			add_filter( $hook_name . '_types', array( $this, 'types' ) );
			foreach ( $conditions as $condition ) {
				if ( isset( $ready_conditions[ $condition ] ) ) {
					// CONDITIONS HTML
					add_filter( $hook_name . '_type_' . $ready_conditions[ $condition ]['type'], array( get_class( $this ), $condition ), 10, 3 );
					// CONDITIONS CHECK
					add_filter( $hook_name . '_check_type_' . $ready_conditions[ $condition ]['type'], array( get_class( $this ), $ready_conditions[ $condition ]['func'] ), 10, 3 );
					if ( ! empty( $ready_conditions[ $condition ]['save'] ) ) {
						add_filter( $hook_name . '_save_type_' . $ready_conditions[ $condition ]['type'], array( get_class( $this ), $ready_conditions[ $condition ]['save'] ), 10, 3 );
					}
				} else {
					do_action( $hook_name . '_condition_not_exist', $condition );
				}
			}
		}
		public function types( $types ) {
			$ready_conditions = static::get_conditions();
			foreach ( $this->conditions as $condition ) {
				if ( isset( $ready_conditions[ $condition ] ) ) {
					$types[ $ready_conditions[ $condition ]['type'] ] = $ready_conditions[ $condition ]['name'];
				}
			}
			return $types;
		}
		public function build( &$value, $additional = array() ) {
			if ( ! is_array( $additional ) ) {
				$additional = array();
			}
			$additional['hook_name'] = $this->hook_name;
			return static::builder( $this->option_name, $value, $additional );
		}
		public static function builder( $name, &$value, $additional = array() ) {
			if ( ! isset( $value ) || ! is_array( $value ) ) {
				$value = array();
			}
			ob_start();
			include plugin_dir_path( __DIR__ ) . 'templates/conditions.php';
			$html = ob_get_clean();
			return $html;
		}
		public static function check( $conditions_data, $hook_name, $additional = array() ) {
			if ( ! is_array( $conditions_data ) || count( $conditions_data ) == 0 ) {
				$condition_status = true;
			} else {
				$condition_status = false;
				foreach ( $conditions_data as $conditions ) {
					$condition_status = false;
					foreach ( $conditions as $condition ) {
						$condition_status = apply_filters( $hook_name . '_check_type_' . $condition['type'], false, $condition, $additional );
						if ( ! $condition_status ) {
							break;
						}
					}
					if ( $condition_status ) {
						break;
					}
				}
			}
			return $condition_status;
		}
		public static function save( $conditions_data, $hook_name ) {
			if ( ! is_array( $conditions_data ) || count( $conditions_data ) == 0 ) {
				$conditions_data = array();
			} else {
				foreach ( $conditions_data as $conditions_id => $conditions ) {
					foreach ( $conditions as $condition_id => $condition ) {
						$conditions_data[ $conditions_id ][ $condition_id ] = apply_filters( $hook_name . '_save_type_' . $condition['type'], $condition );
					}
				}
			}
			return $conditions_data;
		}
		public static function get_conditions() {
			return array(
				// PRODUCTS
				'condition_product'               => array(
					'save' => 'save_condition_product',
					'func' => 'check_condition_product',
					'type' => 'product',
					'name' => __( 'Product', 'Ramphor_domain' ),
				),
				'condition_product_sale'          => array(
					'func' => 'check_condition_product_sale',
					'type' => 'sale',
					'name' => __( 'On Sale', 'Ramphor_domain' ),
				),
				'condition_product_bestsellers'   => array(
					'func' => 'check_condition_product_bestsellers',
					'type' => 'bestsellers',
					'name' => __( 'Bestsellers', 'Ramphor_domain' ),
				),
				'condition_product_price'         => array(
					'func' => 'check_condition_product_price',
					'type' => 'price',
					'name' => __( 'Price', 'Ramphor_domain' ),
				),
				'condition_product_stockstatus'   => array(
					'func' => 'check_condition_product_stockstatus',
					'type' => 'stockstatus',
					'name' => __( 'Stock status', 'Ramphor_domain' ),
				),
				'condition_product_totalsales'    => array(
					'func' => 'check_condition_product_totalsales',
					'type' => 'totalsales',
					'name' => __( 'Total sales', 'Ramphor_domain' ),
				),
				'condition_product_category'      => array(
					'func' => 'check_condition_product_category',
					'type' => 'category',
					'name' => __( 'Category', 'Ramphor_domain' ),
				),
				'condition_product_attribute'     => array(
					'func' => 'check_condition_product_attribute',
					'type' => 'attribute',
					'name' => __( 'Product attribute', 'Ramphor_domain' ),
				),
				'condition_product_age'           => array(
					'func' => 'check_condition_product_age',
					'type' => 'age',
					'name' => __( 'Product age', 'Ramphor_domain' ),
				),
				'condition_product_saleprice'     => array(
					'func' => 'check_condition_product_saleprice',
					'type' => 'saleprice',
					'name' => __( 'Sale price', 'Ramphor_domain' ),
				),
				'condition_product_regularprice'  => array(
					'func' => 'check_condition_product_regularprice',
					'type' => 'regularprice',
					'name' => __( 'Regular price', 'Ramphor_domain' ),
				),
				'condition_product_stockquantity' => array(
					'func' => 'check_condition_product_stockquantity',
					'type' => 'stockquantity',
					'name' => __( 'Stock quantity', 'Ramphor_domain' ),
				),
				'condition_product_featured'      => array(
					'func' => 'check_condition_product_featured',
					'type' => 'featured',
					'name' => __( 'Featured', 'Ramphor_domain' ),
				),
				'condition_product_shippingclass' => array(
					'func' => 'check_condition_product_shippingclass',
					'type' => 'shippingclass',
					'name' => __( 'Shipping Class', 'Ramphor_domain' ),
				),
				'condition_product_type'          => array(
					'func' => 'check_condition_product_type',
					'type' => 'product_type',
					'name' => __( 'Product Type', 'Ramphor_domain' ),
				),
				'condition_product_rating'        => array(
					'func' => 'check_condition_product_rating',
					'type' => 'product_rating',
					'name' => __( 'Product Rating', 'Ramphor_domain' ),
				),
				// PAGES
				'condition_page_id'               => array(
					'func' => 'check_condition_page_id',
					'type' => 'page_id',
					'name' => __( 'Page ID', 'Ramphor_domain' ),
				),
				'condition_page_woo_attribute'    => array(
					'func' => 'check_condition_page_woo_attribute',
					'type' => 'woo_attribute',
					'name' => __( 'Product Attribute', 'Ramphor_domain' ),
				),
				'condition_page_woo_search'       => array(
					'func' => 'check_condition_page_woo_search',
					'type' => 'woo_search',
					'name' => __( 'Product Search', 'Ramphor_domain' ),
				),
				'condition_page_woo_category'     => array(
					'func' => 'check_condition_page_woo_category',
					'type' => 'woo_category',
					'name' => __( 'Product Category', 'Ramphor_domain' ),
				),
			);
		}
		public static function get_condition( $condition ) {
			$conditions = static::get_conditions_product();
			return ( isset( $conditions[ $condition ] ) ? $conditions[ $condition ] : '' );
		}
		public static function supcondition( $name, $options, $extension = array() ) {
			$equal = 'equal';
			if ( is_array( $options ) && isset( $options['equal'] ) ) {
				$equal = $options['equal'];
			}
			$equal_list = array(
				'equal'     => __( 'Equal', 'Ramphor_domain' ),
				'not_equal' => __( 'Not equal', 'Ramphor_domain' ),
			);
			if ( ! empty( $extension['equal_less'] ) ) {
				$equal_list['equal_less'] = __( 'Equal or less', 'Ramphor_domain' );
			}
			if ( ! empty( $extension['equal_more'] ) ) {
				$equal_list['equal_more'] = __( 'Equal or more', 'Ramphor_domain' );
			}
			$html = '<select name="' . $name . '[equal]">';
			foreach ( $equal_list as $equal_slug => $equal_name ) {
				$html .= '<option value="' . $equal_slug . '"' . ( $equal == $equal_slug ? ' selected' : '' ) . '>' . $equal_name . '</option>';
			}
			$html .= '</select>';
			return $html;
		}
		public static function supcondition_check( $value1, $value2, $condition ) {
			$equal = 'equal';
			if ( is_array( $condition ) && isset( $condition['equal'] ) ) {
				$equal = $condition['equal'];
			}
			$check = true;
			switch ( $equal ) {
				case 'equal':
					$check = $value1 == $value2;
					break;
				case 'not_equal':
					$check = $value1 != $value2;
					break;
				case 'equal_less':
					$check = $value1 <= $value2;
					break;
				case 'equal_more':
					$check = $value1 >= $value2;
					break;
			}
			return $check;
		}

		// PRODUCT CONDITION

		// HTML FOR PRODUCT CONDITIONS IN ADMIN PANEL
		public static function condition_product( $html, $name, $options ) {
			$def_options = array( 'product' => array() );
			$options     = array_merge( $def_options, $options );
			$html       .= static::supcondition( $name, $options ) . '
            <div class="br_framework_settings">' . br_products_selector( $name . '[product]', $options['product'] ) . '</div>';
			return $html;
		}

		public static function condition_product_sale( $html, $name, $options ) {
			$def_options = array( 'sale' => 'yes' );
			$options     = array_merge( $def_options, $options );
			$html       .= '<label>' . __( 'Is on sale', 'Ramphor_domain' ) . '<select name="' . $name . '[sale]">
                <option value="yes"' . ( $options['sale'] == 'yes' ? ' selected' : '' ) . '>' . __( 'Yes', 'Ramphor_domain' ) . '</option>
                <option value="no"' . ( $options['sale'] == 'no' ? ' selected' : '' ) . '>' . __( 'No', 'Ramphor_domain' ) . '</option>
            </select></label>';
			return $html;
		}

		public static function condition_product_bestsellers( $html, $name, $options ) {
			$def_options = array( 'bestsellers' => '1' );
			$options     = array_merge( $def_options, $options );
			$html       .= '<label>' . __( 'Count of product', 'Ramphor_domain' ) . '<input type="number" min="1" name="' . $name . '[bestsellers]" value="' . $options['bestsellers'] . '"></label>';
			return $html;
		}

		public static function condition_product_featured( $html, $name, $options ) {
			$html .= static::supcondition( $name, $options );
			return $html;
		}

		public static function condition_product_shippingclass( $html, $name, $options ) {
			$def_options = array( 'term' => '' );
			$options     = array_merge( $def_options, $options );
			$terms       = get_terms(
				array(
					'taxonomy'   => 'product_shipping_class',
					'hide_empty' => false,
				)
			);
			$terms_i     = array();
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$terms_i[ $term->term_id ] = $term->name;
				}
			}
			$html  = static::supcondition( $name, $options );
			$html .= '<select name="' . $name . '[term]">';
			foreach ( $terms_i as $term_id => $term_name ) {
				$html .= '<option value="' . $term_id . '"' . ( $options['term'] == $term_id ? ' selected' : '' ) . '>' . $term_name . '</option>';
			}
			$html .= '</select>';
			return $html;
		}

		public static function condition_product_type( $html, $name, $options ) {
			$def_options   = array( 'product_type' => '' );
			$options       = array_merge( $def_options, $options );
			$html          = static::supcondition( $name, $options );
			$html         .= '<select name="' . $name . '[product_type]">';
			$product_types = wc_get_product_types();
			foreach ( $product_types as $term_id => $term_name ) {
				$html .= '<option value="' . $term_id . '"' . ( $options['product_type'] == $term_id ? ' selected' : '' ) . '>' . $term_name . '</option>';
			}
			$html .= '</select>';
			return $html;
		}
		public static function condition_product_rating( $html, $name, $options ) {
			$def_options = array( 'has_rating' => '' );
			$options     = array_merge( $def_options, $options );
			$html       .= __( 'Has Rating:', 'Ramphor_domain' );
			$html       .= '<select name="' . $name . '[has_rating]">';
			$html       .= '<option value=""' . ( $options['has_rating'] == '' ? ' selected' : '' ) . '>' . __( 'Yes', 'Ramphor_domain' ) . '</option>';
			$html       .= '<option value="no"' . ( $options['has_rating'] == 'no' ? ' selected' : '' ) . '>' . __( 'No', 'Ramphor_domain' ) . '</option>';
			$html       .= '</select>';
			return $html;
		}

		public static function condition_product_price( $html, $name, $options ) {
			$def_options = array(
				'price'     => array(
					'from' => '1',
					'to'   => '1',
				),
				'price_tax' => 'product_price',
			);
			$options     = array_merge( $def_options, $options );
			if ( ! is_array( $options['price'] ) ) {
				$options['price'] = array();
			}
			$options['price'] = array_merge( $def_options['price'], $options['price'] );
			$html            .= static::supcondition( $name, $options );
			$html            .= __( 'From:', 'Ramphor_domain' ) . '<input class="price_from" type="number" min="0" name="' . $name . '[price][from]" value="' . $options['price']['from'] . '">' .
					 __( 'To:', 'Ramphor_domain' ) . '<input class="price_to"   type="number" min="1" name="' . $name . '[price][to]"   value="' . $options['price']['to'] . '">';
			$tax_type         = array(
				'product_price' => __( 'Product price', 'Ramphor_domain' ),
				'with_tax'      => __( 'With tax', 'Ramphor_domain' ),
				'without_tax'   => __( 'Without tax', 'Ramphor_domain' ),
			);
			$html            .= '<select name="' . $name . '[price_tax]">';
			foreach ( $tax_type as $tax_type_val => $tax_type_name ) {
				$html .= '<option value="' . $tax_type_val . '"' . ( $tax_type_val == $options['price_tax'] ? ' selected' : '' ) . '>' . $tax_type_name . '</option>';
			}
			$html .= '</select>';
			return $html;
		}

		public static function condition_product_stockstatus( $html, $name, $options ) {
			$def_options = array( 'stockstatus' => 'in_stock' );
			$options     = array_merge( $def_options, $options );
			$html       .= '
            <select name="' . $name . '[stockstatus]">
                <option value="in_stock"' . ( $options['stockstatus'] == 'in_stock' ? ' selected' : '' ) . '>' . __( 'In stock', 'Ramphor_domain' ) . '</option>
                <option value="out_of_stock"' . ( $options['stockstatus'] == 'out_of_stock' ? ' selected' : '' ) . '>' . __( 'Out of stock', 'Ramphor_domain' ) . '</option>
                <option value="is_on_backorder"' . ( $options['stockstatus'] == 'is_on_backorder' ? ' selected' : '' ) . '>' . __( 'On Backorder', 'Ramphor_domain' ) . '</option>
            </select>';
			return $html;
		}

		public static function condition_product_totalsales( $html, $name, $options ) {
			$def_options = array( 'totalsales' => '1' );
			$options     = array_merge( $def_options, $options );
			$html       .= static::supcondition(
				$name,
				$options,
				array(
					'equal_less' => true,
					'equal_more' => true,
				)
			);
			$html       .= '<label>' . __( 'Count of product', 'Ramphor_domain' ) . '<input type="number" min="0" name="' . $name . '[totalsales]" value="' . $options['totalsales'] . '"></label>';
			return $html;
		}

		public static function condition_product_category( $html, $name, $options ) {
			$def_options = array( 'category' => array() );
			$options     = array_merge( $def_options, $options );
			if ( ! is_array( $options['category'] ) ) {
				$options['category'] = array( $options['category'] );
			}
			$product_categories = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
				)
			);
			if ( is_array( $product_categories ) && count( $product_categories ) > 0 ) {
				$def_options = array( 'category' => '' );
				$options     = array_merge( $def_options, $options );
				$html       .= static::supcondition( $name, $options );
				$html       .= '<label><input type="checkbox" name="' . $name . '[subcats]" value="1"' . ( empty( $options['subcats'] ) ? '' : ' checked' ) . '>' . __( 'Include subcategories', 'Ramphor_domain' ) . '</label>';
				$html       .= '<div style="max-height:150px;overflow:auto;border:1px solid #ccc;padding: 5px;">';
				foreach ( $product_categories as $category ) {
					$html .= '<div><label>
                    <input type="checkbox" name="' . $name . '[category][]" value="' . $category->term_id . '"' . ( ( ! empty( $options['category'] ) && is_array( $options['category'] ) && in_array( $category->term_id, $options['category'] ) ) ? ' checked' : '' ) . '>
                    ' . $category->name . '
                    </label></div>';
				}
				$html .= '</div>';
			}
			return $html;
		}

		public static function condition_product_attribute( $html, $name, $options ) {
			$def_options        = array( 'attribute' => '' );
			$options            = array_merge( $def_options, $options );
			$attributes         = get_object_taxonomies( 'product', 'objects' );
			$product_attributes = array();
			foreach ( $attributes as $attribute ) {
				$attribute_i          = array();
				$attribute_i['name']  = $attribute->name;
				$attribute_i['label'] = $attribute->label;
				$attribute_i['value'] = array();
				$terms                = get_terms(
					array(
						'taxonomy'   => $attribute->name,
						'hide_empty' => false,
					)
				);
				foreach ( $terms as $term ) {
					$attribute_i['value'][ $term->term_id ] = $term->name;
				}
				$product_attributes[] = $attribute_i;
			}
			$html             .= static::supcondition( $name, $options );
			$html             .= '<label>' . __( 'Select attribute', 'Ramphor_domain' ) . '</label>';
			$html             .= '<select name="' . $name . '[attribute]" class="br_cond_attr_select">';
			$has_selected_attr = false;
			foreach ( $product_attributes as $attribute ) {
				$html .= '<option value="' . $attribute['name'] . '"' . ( isset( $options['attribute'] ) && $attribute['name'] == $options['attribute'] ? ' selected' : '' ) . '>' . $attribute['label'] . '</option>';
				if ( $attribute['name'] == $options['attribute'] ) {
					$has_selected_attr = true;
				}
			}
			$html         .= '</select>';
			$is_first_attr = ! $has_selected_attr;
			foreach ( $product_attributes as $attribute ) {
				$html .= '<select class="br_attr_values br_attr_value_' . $attribute['name'] . '" name="' . $name . '[values][' . $attribute['name'] . ']"' . ( $is_first_attr || $attribute['name'] == $options['attribute'] ? '' : ' style="display:none;"' ) . '>';
				$html .= '<option value="">==Any==</option>';
				foreach ( $attribute['value'] as $term_id => $term_name ) {
					$html .= '<option value="' . $term_id . '"' . ( ! empty( $options['values'][ $attribute['name'] ] ) && $options['values'][ $attribute['name'] ] == $term_id ? ' selected' : '' ) . '>' . $term_name . '</option>';
				}
				$html         .= '</select>';
				$is_first_attr = false;
			}
			return $html;
		}

		public static function condition_product_age( $html, $name, $options ) {
			$def_options = array( 'age' => '1' );
			$options     = array_merge( $def_options, $options );
			$html       .= br_supcondition_equal(
				$name,
				$options,
				array(
					'equal_less' => true,
					'equal_more' => true,
				)
			);
			$html       .= '<input type="number" min="0" name="' . $name . '[age]" value="' . $options['age'] . '">' . __( 'day(s)', 'Ramphor_domain' );
			return $html;
		}

		public static function condition_product_saleprice( $html, $name, $options ) {
			$def_options = array(
				'saleprice' => array(
					'from' => '1',
					'to'   => '1',
				),
			);
			$options     = array_merge( $def_options, $options );
			if ( ! is_array( $options['saleprice'] ) ) {
				$options['saleprice'] = array();
			}
			$options['price'] = array_merge( $def_options['saleprice'], $options['saleprice'] );
			$html            .= br_supcondition_equal( $name, $options );
			$html            .= __( 'From:', 'Ramphor_domain' ) . '<input class="price_from" type="number" min="0" name="' . $name . '[saleprice][from]" value="' . $options['saleprice']['from'] . '">' .
					 __( 'To:', 'Ramphor_domain' ) . '<input class="price_to"   type="number" min="1" name="' . $name . '[saleprice][to]"   value="' . $options['saleprice']['to'] . '">';
			return $html;
		}

		public static function condition_product_regularprice( $html, $name, $options ) {
			$def_options = array(
				'regularprice' => array(
					'from' => '1',
					'to'   => '1',
				),
			);
			$options     = array_merge( $def_options, $options );
			if ( ! is_array( $options['regularprice'] ) ) {
				$options['regularprice'] = array();
			}
			$options['price'] = array_merge( $def_options['regularprice'], $options['regularprice'] );
			$html            .= br_supcondition_equal( $name, $options );
			$html            .= __( 'From:', 'Ramphor_domain' ) . '<input class="price_from" type="number" min="0" name="' . $name . '[regularprice][from]" value="' . $options['regularprice']['from'] . '">' .
					 __( 'To:', 'Ramphor_domain' ) . '<input class="price_to"   type="number" min="1" name="' . $name . '[regularprice][to]"   value="' . $options['regularprice']['to'] . '">';
			return $html;
		}

		public static function condition_product_stockquantity( $html, $name, $options ) {
			$def_options = array(
				'stockquantity' => '1',
				'backorder'     => 'any',
			);
			$options     = array_merge( $def_options, $options );
			$html       .= br_supcondition_equal(
				$name,
				$options,
				array(
					'equal_less' => true,
					'equal_more' => true,
				)
			);
			$html       .= __( 'Products in stock', 'Ramphor_domain' );
			$html       .= '<input type="number" min="0" name="' . $name . '[stockquantity]" value="' . $options['stockquantity'] . '">';
			$html       .= '<label>' . __( 'Backorder allowed', 'Ramphor_domain' ) . ' <select name="' . $name . '[backorder]">
                <option value="any"' . ( $options['backorder'] == 'any' ? ' selected' : '' ) . '>' . __( 'Any', 'Ramphor_domain' ) . '</option>
                <option value="yes"' . ( $options['backorder'] == 'yes' ? ' selected' : '' ) . '>' . __( 'Yes', 'Ramphor_domain' ) . '</option>
                <option value="no"' . ( $options['backorder'] == 'no' ? ' selected' : '' ) . '>' . __( 'No', 'Ramphor_domain' ) . '</option>
            </select></label>';
			return $html;
		}

		// SAVE PRODUCT CONDITIONS
		public static function save_condition_product( $condition ) {
			if ( isset( $condition['product'] ) && is_array( $condition['product'] ) ) {
				$condition['additional_product'] = array();
				foreach ( $condition['product'] as $product ) {
					$wc_product = wc_get_product( $product );
					if ( $wc_product->get_type() == 'grouped' ) {
						$children = $wc_product->get_children();
						if ( ! is_array( $children ) ) {
							$children = array();
						}
						$condition['additional_product'] = array_merge( $condition['additional_product'], $children );
					}
				}
			}
			return $condition;
		}

		// CHECK PRODUCT CONDITIONS
		public static function check_condition_product( $show, $condition, $additional ) {
			if ( isset( $condition['product'] ) && is_array( $condition['product'] ) ) {
				$show = in_array( $additional['product_id'], $condition['product'] );
				if ( ! empty( $condition['additional_product'] ) && is_array( $condition['additional_product'] ) ) {
					$show = $show || in_array( $additional['product_id'], $condition['additional_product'] );
				}
				if ( $condition['equal'] == 'not_equal' ) {
					$show = ! $show;
				}
			}
			return $show;
		}

		public static function check_condition_product_sale( $show, $condition, $additional ) {
			$show = $additional['product']->is_on_sale();
			if ( $condition['sale'] == 'no' ) {
				$show = ! $show;
			}
			return $show;
		}

		public static function check_condition_product_bestsellers( $show, $condition, $additional ) {
			$args  = array(
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => $condition['bestsellers'],
				'meta_key'            => 'total_sales',
				'orderby'             => 'meta_value_num',
				'tax_query'           => array(
					array(
						'taxonomy' => 'product_visibility',
						'field'    => 'slug',
						'terms'    => array( 'exclude-from-catalog' ),
						'operator' => 'NOT IN',
					),
				),
			);
			$posts = get_posts( $args );
			if ( is_array( $posts ) ) {
				foreach ( $posts as $post ) {
					if ( $additional['product_id'] == $post->ID ) {
						$show = true;
						break;
					}
				}
			}
			return $show;
		}

		public static function check_condition_product_featured( $show, $condition, $additional ) {
			$show = function_exists( 'wc_get_product_visibility_term_ids' );
			if ( $show ) {
				$terms_id = wc_get_product_visibility_term_ids();
				$show     = ! empty( $terms_id['featured'] );
				if ( $show ) {
					$show  = false;
					$terms = get_the_terms( $additional['product_id'], 'product_visibility' );
					if ( is_array( $terms ) ) {
						foreach ( $terms as $term ) {
							if ( $term->term_id == $terms_id['featured'] ) {
								$show = true;
								break;
							}
						}
					}
				}
			}
			if ( $condition['equal'] == 'not_equal' ) {
				$show = ! $show;
			}
			return $show;
		}

		public static function check_condition_product_shippingclass( $show, $condition, $additional ) {
			$terms = get_the_terms( $additional['product_id'], 'product_shipping_class' );
			if ( is_array( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( $term->term_id == $condition['term'] ) {
						$show = true;
						break;
					}
				}
			}
			if ( $condition['equal'] == 'not_equal' ) {
				$show = ! $show;
			}
			return $show;
		}

		public static function check_condition_product_type( $show, $condition, $additional ) {
			$show = $additional['product']->is_type( $condition['product_type'] );
			if ( $condition['equal'] == 'not_equal' ) {
				$show = ! $show;
			}
			return $show;
		}
		public static function check_condition_product_rating( $show, $condition, $additional ) {
			$show = ( $additional['product']->get_average_rating() > 0 );
			if ( $condition['has_rating'] == 'no' ) {
				$show = ! $show;
			}
			return $show;
		}

		public static function check_condition_product_price( $show, $condition, $additional ) {
			$def_options   = array(
				'price'     => array(
					'from' => '1',
					'to'   => '1',
				),
				'price_tax' => 'product_price',
			);
			$condition     = array_merge( $def_options, $condition );
			$product_price = br_wc_get_product_attr( $additional['product'], 'price' );
			$show          = self::check_tax_price_for_variations( $additional['product'], $condition['price_tax'], $condition['price']['from'], $condition['price']['to'] );
			if ( $condition['equal'] == 'not_equal' ) {
				$show = ! $show;
			}
			return $show;
		}

		public static function check_condition_product_stockstatus( $show, $condition, $additional ) {
			if ( $condition['stockstatus'] == 'is_on_backorder' ) {
				$show = $additional['product']->is_on_backorder();
			} else {
				$show = $additional['product']->is_in_stock();
				if ( $condition['stockstatus'] == 'out_of_stock' ) {
					$show = ! $show;
				}
			}
			return $show;
		}

		public static function check_condition_product_totalsales( $show, $condition, $additional ) {
			$total_sales = get_post_meta( $additional['product_id'], 'total_sales', true );
			$show        = static::supcondition_check( $total_sales, $condition['totalsales'], $condition );
			return $show;
		}

		public static function check_condition_product_category( $show, $condition, $additional ) {
			if ( ! is_array( $condition['category'] ) ) {
				$condition['category'] = array( $condition['category'] );
			}
			$terms = get_the_terms( $additional['product_id'], 'product_cat' );
			if ( is_array( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( in_array( $term->term_id, $condition['category'] ) ) {
						$show = true;
					}
					if ( ! empty( $condition['subcats'] ) && ! $show ) {
						foreach ( $condition['category'] as $category ) {
							$show = term_is_ancestor_of( $category, $term->term_id, 'product_cat' );
							if ( $show ) {
								break;
							}
						}
					}
					if ( $show ) {
						break;
					}
				}
			}
			if ( $condition['equal'] == 'not_equal' ) {
				$show = ! $show;
			}
			return $show;
		}

		public static function check_condition_product_attribute( $show, $condition, $additional ) {
			$terms = get_the_terms( $additional['product_id'], $condition['attribute'] );
			$show  = false;
			if ( is_array( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( $term->term_id == $condition['values'][ $condition['attribute'] ] || $condition['values'][ $condition['attribute'] ] === '' ) {
						$show = true;
						break;
					}
				}
			}
			if ( $condition['equal'] == 'not_equal' ) {
				$show = ! $show;
			}
			return $show;
		}

		public static function check_condition_product_age( $show, $condition, $additional ) {
			$post_date = $additional['product_post']->post_date;
			$post_date = date( 'Y-m-d', strtotime( $post_date ) );
			$value     = $condition['age'];
			$test_date = date( 'Y-m-d', strtotime( "-$value days", time() ) );
			$show      = static::supcondition_check( $test_date, $post_date, $condition );
			return $show;
		}

		public static function check_condition_product_saleprice( $show, $condition, $additional ) {
			$product_sale = br_wc_get_product_attr( $additional['product'], 'sale_price' );
			$show         = self::check_any_price_for_variations( $additional['product'], 'sale_price', $condition['saleprice']['from'], $condition['saleprice']['to'] );
			if ( $condition['equal'] == 'not_equal' ) {
				$show = ! $show;
			}
			return $show;
		}

		public static function check_condition_product_regularprice( $show, $condition, $additional ) {
			$product_sale = br_wc_get_product_attr( $additional['product'], 'regular_price' );
			$show         = self::check_any_price_for_variations( $additional['product'], 'regular_price', $condition['regularprice']['from'], $condition['regularprice']['to'] );
			if ( $condition['equal'] == 'not_equal' ) {
				$show = ! $show;
			}
			return $show;
		}

		public static function check_any_price_for_variations( $product, $price_field = 'price', $price_from = 1, $price_to = 10 ) {
			if ( $product->is_type( 'variable' ) ) {
				$show               = false;
				$product_variations = $product->get_available_variations();
				foreach ( $product_variations as $product_variation ) {
					$variation_product = new WC_Product_Variation( $product_variation['variation_id'] );
					$product_sale      = br_wc_get_product_attr( $variation_product, $price_field );
					if ( $product_sale >= $price_from && $product_sale <= $price_to ) {
						$show = true;
						return $show;
					}
				}
			} else {
				$product_sale = br_wc_get_product_attr( $product, $price_field );
				$show         = $product_sale >= $price_from && $product_sale <= $price_to;
			}
			return $show;
		}

		public static function check_tax_price_for_variations( $product, $variant, $price_from = 1, $price_to = 10 ) {
			if ( $variant == 'with_tax' || $variant == 'without_tax' ) {
				$tax_function = 'wc_get_price_including_tax';
				if ( $variant == 'without_tax' ) {
					$tax_function = 'wc_get_price_excluding_tax';
				}
				if ( $product->is_type( 'variable' ) ) {
					$show               = false;
					$product_variations = $product->get_available_variations();
					foreach ( $product_variations as $product_variation ) {
						$variation_product = new WC_Product_Variation( $product_variation['variation_id'] );
						$product_sale      = $tax_function( $variation_product );
						if ( $product_sale >= $price_from && $product_sale <= $price_to ) {
							$show = true;
							return $show;
						}
					}
				} else {
					$product_sale = $tax_function( $product );
					$show         = $product_sale >= $price_from && $product_sale <= $price_to;
				}
			} else {
				$show = self::check_any_price_for_variations( $product, 'price', $price_from, $price_to );
			}
			return $show;
		}

		public static function check_condition_product_stockquantity( $show, $condition, $additional ) {
			$product = $additional['product'];
			if ( method_exists( $product, 'get_stock_quantity' ) ) {
				$product_stock = $product->get_stock_quantity( 'edit' );
			} else {
				$product_stock = $product->stock;
			}
			$backorder = true;
			if ( ! empty( $condition['backorder'] ) && $condition['backorder'] != 'any' ) {
				$backorder = $additional['product']->backorders_allowed();
				if ( $condition['backorder'] == 'no' ) {
					$backorder = ! $backorder;
				}
			}
			$show = static::supcondition_check( $product_stock, $condition['stockquantity'], $condition );
			$show = $show && $backorder;
			return $show;
		}
		// PAGE CONDITIONS

		// HTML FOR PAGE CONDITIONS IN ADMIN PANEL

		public static function condition_page_id( $html, $name, $options ) {
			$def_options = array( 'pages' => array() );
			$options     = array_merge( $def_options, $options );
			$html       .= br_supcondition_equal( $name, $options );
			$pages       = get_pages();
			$html       .= '<div style="max-height:150px;overflow:auto;border:1px solid #ccc;padding: 5px;">';
			$woo_pages   = array(
				'shop'       => '[SHOP PAGE]',
				'product'    => '[PRODUCT PAGE]',
				'category'   => '[PRODUCT CATEGORY PAGE]',
				'taxonomies' => '[PRODUCT TAXONOMIES]',
				'tags'       => '[PRODUCT TAGS]',
			);
			foreach ( $woo_pages as $page_id => $page_name ) {
				$html .= '<div><label><input name="' . $name . '[pages][]" type="checkbox" value="' . $page_id . '"' . ( in_array( $page_id, $options['pages'] ) ? ' checked' : '' ) . '>' . $page_name . '</label></div>';
			}
			foreach ( $pages as $page ) {
				$html .= '<div><label><input name="' . $name . '[pages][]" type="checkbox" value="' . $page->ID . '"' . ( in_array( $page->ID, $options['pages'] ) ? ' checked' : '' ) . '>' . $page->post_title . ' (ID: ' . $page->ID . ')</label></div>';
			}
			$html .= '</div>';
			return $html;
		}

		public static function condition_page_woo_attribute( $html, $name, $options ) {
			return self::condition_product_attribute( $html, $name, $options );
		}

		public static function condition_page_woo_search( $html, $name, $options ) {
			$def_options = array( 'search' => array() );
			$options     = array_merge( $def_options, $options );
			$html       .= br_supcondition_equal( $name, $options );
			return $html;
		}

		public static function condition_page_woo_category( $html, $name, $options ) {
			return self::condition_product_category( $html, $name, $options );
		}

		// CHECK PAGE CONDITIONS

		public static function check_condition_page_id( $show, $condition, $additional ) {
			$show        = false;
			$def_options = array( 'pages' => array() );
			$condition   = array_merge( $def_options, $condition );
			if ( is_array( $condition['pages'] ) && count( $condition['pages'] ) != 0 ) {
				if ( function_exists( 'is_shop' ) && function_exists( 'is_product_category' ) && function_exists( 'is_product' ) ) {
					if ( is_shop() && in_array( 'shop', $condition['pages'] )
					|| is_product_category() && in_array( 'category', $condition['pages'] )
					|| is_product() && in_array( 'product', $condition['pages'] )
					|| is_product_tag() && in_array( 'tags', $condition['pages'] )
					|| is_product_taxonomy() && in_array( 'taxonomies', $condition['pages'] ) ) {
						$show = true;
					}
				}
				$remove_elements = array( 'shop', 'category', 'product' );
				foreach ( $remove_elements as $remove_element ) {
					$remove_i = array_search( $remove_element, $condition['pages'] );
					if ( $remove_i !== false ) {
						unset( $condition['pages'][ $remove_i ] );
					}
				}
				if ( ! empty( $condition['pages'] ) && is_page( $condition['pages'] ) ) {
					$show = true;
				}
			}
			if ( $condition['equal'] == 'not_equal' ) {
				$show = ! $show;
			}
			return $show;
		}

		public static function check_condition_page_woo_attribute( $show, $condition, $additional ) {
			$show = ( is_tax( $condition['attribute'], $condition['values'][ $condition['attribute'] ] ) );
			if ( $condition['equal'] == 'not_equal' ) {
				$show = ! $show;
			}
			return $show;
		}

		public static function check_condition_page_woo_search( $show, $condition, $additional ) {
			$show = ( is_search() );
			if ( $condition['equal'] == 'not_equal' ) {
				$show = ! $show;
			}
			return $show;
		}

		public static function check_condition_page_woo_category( $show, $condition, $additional ) {
			global $wp_query;
			$show = false;
			if ( ! empty( $condition['category'] ) && ! is_array( $condition['category'] ) ) {
				$condition['category'] = array( $condition['category'] );
			}
			if ( $wp_query->is_tax ) {
				$queried_object = $wp_query->get_queried_object();
				if ( ! empty( $condition['category'] )
				&& is_array( $condition['category'] )
				&& is_object( $queried_object )
				&& property_exists( $queried_object, 'term_id' )
				&& property_exists( $queried_object, 'taxonomy' )
				&& $queried_object->taxonomy == 'product_cat' ) {
					$show = in_array( $queried_object->term_id, $condition['category'] );
					if ( empty( $show ) && ! empty( $condition['subcats'] ) ) {
						foreach ( $condition['category'] as $category ) {
							$show = term_is_ancestor_of( $category, $queried_object, 'product_cat' );
							if ( $show ) {
								break;
							}
						}
					}
				}
			}
			if ( $condition['equal'] == 'not_equal' ) {
				$show = ! $show;
			}
			return $show;
		}
	}
}
