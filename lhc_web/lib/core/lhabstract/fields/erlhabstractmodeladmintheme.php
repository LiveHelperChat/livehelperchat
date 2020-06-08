<?php

return array(
    // Messages styles
    'buble_visitor_background' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Bubble background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'buble_visitor_title_color' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Title color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'buble_visitor_text_color' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'buble_operator_background' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','My messages bubble background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'buble_operator_other_background' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Other operator bubble background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'buble_operator_title_color' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Title color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'buble_operator_other_title_color' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Title color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'buble_operator_text_color' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'buble_operator_other_text_color' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'chat_bg' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Chat background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'time_color' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Time text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),

    'buble_sys_background' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Bubble background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),

    'buble_sys_title_color' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Title text colorr'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),

    'buble_sys_text_color' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Message text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),

    // Main styles
    'main_background_color' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Main background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'link_tc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Link default text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'default_tc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Default text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'drpdown_bgc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Dropdown background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'nvb_li_clr' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Navigation bar icons text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
     'nvb_lih_clr' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Navigation bar icons hover text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'tab_boc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Tab border color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'btnd_clr' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    ),
    'btnd_bc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    ),
    'btnd_boc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button border color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    ),

    'btnda_clr' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button active text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    ),
    'btnda_bc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button active background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    ),
    'btnda_boc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button active border color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    ),


    'btnp_clr' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    ),
    'btnp_bc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    ),
    'btnp_boc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button border color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    ),

    'btnpa_clr' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button active text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    ),
    'btnpa_bc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button active background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    ),
    'btnpa_boc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button active border color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    ),







    'drpdown_hbgc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Dropdown hover background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'drpdown_boc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Dropdown border color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'chat_onl_bc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Online background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'panel_background_color' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Panel header background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'panel_border_color' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Panel header border color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'nvbar_bc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Header background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'panel_mbc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Panel background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'tab_bc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Active tab background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'bcrumb_bgc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Breadcrumb background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'bcrumb_boc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Breadcrumb border color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'mactive_bc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Menu active background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'men_col' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Menu text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'tab_tc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Tab text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'tbl_boc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Table border color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'nvb_tgl_bgc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Navbar toggle background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'nvb_tgl_bc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Navbar toggle border color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'tab_atc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Tab active text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'nvbar_pbc' => array(
        'type' => 'colorpicker',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Header border color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'header_font_size' => array(
        'type' => 'text',
        'main_attr' => 'css_attributes_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button border radius'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ))
);