var br_saved_timeout;
var br_savin_ajax = false;
var br_each_parent_tr;
(function ($){
	br_each_parent_tr = function(selector, hide, thtd) {
		var better_position = $( '.berocket_label_better_position' ).prop( 'checked' );
		$( selector ).each(
			function(i, o) {
				if ( $( o ).is( '.berocket_label_better_position_hide' ) && better_position || $( o ).is( '.berocket_label_better_position_show' ) && ! better_position) {
					hide = true;
				}
				var whathide = $( o ).parents( 'tr' ).first();
				if ( thtd ) {
					whathide = whathide.find( 'th, td' );
				}
				if ( hide ) {
					whathide.hide();
				} else {
					whathide.show();
				}
			}
		);
	};

	$( document ).ready(
		function () {
				$( document ).on(
					'change',
					'.berocket_label_content_type',
					function() {
						br_each_parent_tr( '.berocket_label_', true, false );
						br_each_parent_tr( '.berocket_label_' + $( this ).val(), false, false );
					}
				);
				$( document ).on(
					'change',
					'.berocket_label_type_select',
					function() {
						br_each_parent_tr( '.berocket_label_type_', true, false );
						br_each_parent_tr( '.berocket_label_type_' + $( this ).val(), false, false );
					}
				);
				$( document ).on(
					'change',
					'.br_label_backcolor_use',
					function() {
						br_each_parent_tr( '.br_label_backcolor', ! $( this ).prop( 'checked' ), false );
					}
				);
				$( document ).on(
					'change',
					'.pos_label',
					function() {
						br_each_parent_tr( '.pos_label_', true, true );
						br_each_parent_tr( '.pos_label_' + $( this ).val(), false, true );
						$( '.pos__' ).hide();
						$( '.pos__' + $( this ).val() ).show();
					}
				);

				var br_label_ajax_demo = null;
				$( document ).on(
					'change',
					'.br_alabel_settings input, .br_alabel_settings textarea, .br_alabel_settings select, input[name="br_labels[template]"]',
					function() {
						if ( $( this ).is( '.br_not_change' ) ) {
							if ( $( this ).attr( 'name' ) == 'br_labels[template]' ) {
								br_apply_template_values( $( this ) );
							}
						} else if ( $( this ).is( '.br_js_change' ) ) {
							if ( $( this ).data( 'style' ) && $( this ).data( 'style' ).search( 'use:' ) != -1 ) {
								style = $( this ).data( 'style' );
								style = style.replace( 'use:', '' );
								if ( $( this ).is( '[type=checkbox]' ) ) {
									if ( $( this ).prop( 'checked' ) ) {
										value = $( '[data-style=' + style + ']' ).val();
									} else {
										value = '';
									}
								} else {
									value = $( this ).val();
								}
							} else {
								if ( $( this ).val().length ) {
									var use_ext = true;
									if ( $( this ).data( 'notext' ) ) {
										var search_val = $( this ).val();
										var notext     = $( this ).data( 'notext' );
										notext         = notext.split( ',' );
										notext.forEach(
											function(notext_element) {
												if ( search_val.search( notext_element ) != -1 ) {
													use_ext = false;
												}
											}
										);
									}
									if ( use_ext ) {
										if ( $( this ).data( 'ext' ).search( 'VAL' ) == -1 ) {
											var value = $( this ).val() + $( this ).data( 'ext' );
										} else {
											var value = $( this ).data( 'ext' ).replace( 'VAL', $( this ).val() );
										}
									} else {
										var value = $( this ).val();
									}
								} else {
									var value = $( this ).val();
								}
								if ( $( this ).data( 'from' ) ) {
									var style = $( $( this ).data( 'from' ) ).val();
								} else {
									var style = $( this ).data( 'style' );
								}
							}
							$( '.berocket_label_preview' ).find( $( this ).data( 'for' ) ).css( style, value );
							if ( style == 'background-color' ) {
								$( '.berocket_label_preview' ).find( $( this ).data( 'for' ) ).find( 'i' )
								.css( style, value )
								.css( 'border-color', value );
							}
						} else {
							var form_data = $( this ).parents( 'form#post' ).serialize();
							$( '.berocket_label_preview .br_alabel' ).remove();
							if ( br_label_ajax_demo != null ) {
								br_label_ajax_demo.abort();
							}
							br_label_ajax_demo = $.post(
								ajaxurl,
								form_data + '&action=br_label_ajax_demo',
								function(data) {
									$( '.berocket_label_preview .br_alabel' ).remove();
									$( '.berocket_label_preview' ).append( data );
									br_label_ajax_demo = null;
									$( '.tippy-popper' ).remove();
									if ( typeof(berocket_regenerate_tooltip) != 'undefined' ) {
										berocket_regenerate_tooltip();
									}
								}
							);
						}
					}
				);
				$( '.berocket_label_content_type, .berocket_label_type_select, .br_label_backcolor_use, .pos_label, .br_label_templates_use' ).trigger( 'change' );
				/*if ( ! $('input[name="br_labels[template]"]:checked').length ) {
				$('input[name="br_labels[template]"]').first().click();
				}*/
				$( document ).on(
					'mousedown',
					'.br_template_select li',
					function() {
						var $radio = $( this ).find( 'input[type=radio]' );
						if ( $radio.prop( 'checked' ) ) {
							  $( this ).addClass( 'waschecked' );
						}
					}
				);
				$( document ).on(
					'mouseup',
					'.br_template_select li',
					function() {
						var $radio = $( this ).find( 'input[type=radio]' );
						if ( $( this ).is( '.waschecked' ) ) {
							  $( this ).removeClass( 'waschecked' );
							setTimeout(
								function() {
									$radio.prop( 'checked', false );
									var $template_values = berocket_products_label_admin.custom_post_default_set;
									br_set_template_values( $template_values );
									br_hide_selected_template_options();
									$( 'select[name="br_labels[type]"]' ).trigger( 'change' );
								},
								10
							);
						}
					}
				);
				$( document ).on(
					'change',
					'.berocket_label_attribute_type_select .br_colorpicker_value',
					function() {
						$( '.berocket_color_image_term_' + $( this ).data( 'term_id' ) ).css( 'background-color', $( this ).val() );
					}
				);
				$( document ).on(
					'change',
					'.berocket_label_attribute_type_select .berocket_image_value',
					function() {
						var term_id   = $( this ).data( 'term_id' );
						var $item     = $( '.berocket_color_image_term_' + term_id );
						var term_name = $( this ).data( 'term_name' );
						var value     = $( this ).val();
						if ( ! value || value.substring( 0, 3 ) != 'fa-' ) {
							  var replace_to = '<img class="berocket_color_image_term_' + term_id + ' berocket_widget_icon" src="' + value + '" alt="' + term_name + '" title="' + term_name + '">';
						} else {
							 var replace_to = '<i class="berocket_color_image_term_' + term_id + ' fa ' + value + '" title="' + term_name + '"></i>';
						}
						$item.replaceWith( $( replace_to ) );
					}
				);
		}
	);

	$( document ).on(
		"click",
		'.br_settings_vtab',
		function (event) {
			event.preventDefault();
			$( '.br_settings_vtab.active' ).removeClass( 'active' );
			$( this ).addClass( 'active' );

			$( '.br_settings_vtab-content.active' ).removeClass( 'active' );
			$( '.br_settings_vtab-content.tab-' + $( this ).data( 'tab' ) ).addClass( 'active' );
		}
	);

	function br_apply_template_values( $obj ) {
		var $template_values = berocket_products_label_admin.custom_post_default;
		br_set_template_values( $template_values );
		br_set_template_values( $obj.data() );
		br_hide_selected_template_options();
		$( 'select[name="br_labels[type]"]' ).trigger( 'change' );
	}

	function br_set_template_values( template_values ) {
		$.each(
			template_values,
			function (key, value) {
				$el = $( 'input[name="br_labels[' + key + ']"], select[name="br_labels[' + key + ']"], textarea[name="br_labels[' + key + ']"]' ).first();
				if ( $el.length ) {
					if ( $el.is( ':checkbox' ) ) {
						$el.prop( "checked", (value * 1 > 0) );
					} else if ( $el.is( 'input' ) || $el.is( 'textarea' ) || $el.is( 'select' ) ) {
						$el.val( value );
					}
				}
			}
		);
	}
	function br_hide_selected_template_options() {
		br_template_hide_class_for_elements( berocket_products_label_admin.custom_post_setting_names, false );
		var $selected_template = $( '.br_label_css_templates input[name="br_labels[template]"]:checked' );
		if ( $selected_template.length ) {
			br_template_hide_class_for_elements( $selected_template.data( 'template_hide' ), true );
		}
	}
	function br_template_hide_class_for_elements (elements, add) {
		if ( typeof(elements) == 'object' ) {
			$.each(
				elements,
				function(i, key) {
					var $element = $( 'input[name="br_labels[' + key + ']"], select[name="br_labels[' + key + ']"], textarea[name="br_labels[' + key + ']"]' );
					if ( add ) {
						$element.parents( 'tr' ).addClass( 'berocket_template_hide_not_worked_option' );
					} else {
						$element.parents( 'tr' ).removeClass( 'berocket_template_hide_not_worked_option' );
					}
				}
			);
		}
	}
	br_hide_selected_template_options();
})( jQuery );
