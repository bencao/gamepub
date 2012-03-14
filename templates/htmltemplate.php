<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Low-level generator for HTML
 *
 * PHP version 5
 *
 * @category  Output
 * @package   ShaiShai
 * @copyright 2008 ShaiShai, Inc.
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/templates/xmltemplate.php';

/**
 * Low-level generator for HTML
 *
 * Abstracts some of the code necessary for HTML generation. Especially
 * has methods for generating HTML form elements. Note that these have
 * been created kind of haphazardly, not with an eye to making a general
 * HTML-creation class.
 *
 * @category Output
 * @package  ShaiShai
 *
 * @see      Action
 * @see      XMLOutputter
 */

class HTMLTemplate extends XMLTemplate
{
    /**
     * Constructor
     *
     * Just wraps the XMLOutputter constructor.
     *
     * @param string  $output URI to output to, default = stdout
     * @param boolean $indent Whether to indent output, default true
     */

    function __construct($output='php://output', $indent=true)
    {
        parent::__construct($output, $indent);
    }

    /**
     * Start an HTML document
     *
     * If $type isn't specified, will attempt to do content negotiation.
     *
     * Attempts to do content negotiation for language, also.
     *
     * @param string $type MIME type to use; default is to do negotation.
     *
     * @todo extract content negotiation code to an HTTP module or class.
     *
     * @return void
     */

    function startHTML($type='text/html; charset=utf-8')
    {
    	header('Content-Type: '.$type);
        $this->extraHeaders();
        $this->xw->writeDTD('html');
		$this->elementStart('html', array('lang' => 'zh_CN'));
    }

    /**
    *  Ends an HTML document
    *
    *  @return void
    */
    function endHTML()
    {
        $this->elementEnd('html');
        $this->endXML();
    }

    /**
    *  To specify additional HTTP headers for the action
    *
    *  @return void
    */
    function extraHeaders()
    {
        // Needs to be overloaded
    }

    /**
     * Output an HTML text input element
     *
     * Despite the name, it is specifically for outputting a
     * text input element, not other <input> elements. It outputs
     * a cluster of elements, including a <label> and an associated
     * instructions span.
     *
     * @param string $id           element ID, must be unique on page
     * @param string $label        text of label for the element
     * @param string $value        value of the element, default null
     * @param string $instructions instructions for valid input
     *
     * @todo add a $name parameter
     * @todo add a $maxLength parameter
     * @todo add a $size parameter
     *
     * @return void
     */

    function input($id, $label, $value=null, $instructions=null, $readonly=false, $size = 16)
    {
        $this->element('label', array('for' => $id), $label);
        $attrs = array('name' => $id,
                       'type' => 'text',
                       'id' => $id);
        if ($value) {
            $attrs['value'] = $value;
            $attrs['size'] = $size;
            if ($readonly) {
            	$attrs['readOnly'] = 'true' ;
            }
        }
        $this->element('input', $attrs);
        if ($instructions) {
            $this->element('p', 'form_guide', $instructions);
        }
    }

    /**
     * output an HTML checkbox and associated elements
     *
     * Note that the value is default 'true' (the string), which can
     * be used by Action::boolean()
     *
     * @param string $id           element ID, must be unique on page
     * @param string $label        text of label for the element
     * @param string $checked      if the box is checked, default false
     * @param string $instructions instructions for valid input
     * @param string $value        value of the checkbox, default 'true'
     * @param string $disabled     show the checkbox disabled, default false
     *
     * @return void
     *
     * @todo add a $name parameter
     */

    function checkbox($id, $label, $checked=false, $instructions=null,
                      $value='true', $disabled=false, $name=null, $class='checkbox')
    {
    	if ($name == null) {
    		$name = $id;
    	}
        $attrs = array('name' => $name,
                       'type' => 'checkbox',
                       'class' => $class,
                       'id' => $id);
        if ($value) {
            $attrs['value'] = $value;
        }
        if ($checked) {
            $attrs['checked'] = 'checked';
        }
        if ($disabled) {
            $attrs['disabled'] = 'true';
        }
        $this->element('input', $attrs);
        $this->text(' ');
        $this->element('label', array('class' => $class,
                                      'for' => $id),
                       $label);
        $this->text(' ');
        if ($instructions) {
            $this->element('p', 'form_guide', $instructions);
        }
    }

    /**
     * output an HTML combobox/select and associated elements
     *
     * $content is an array of key-value pairs for the dropdown, where
     * the key is the option value attribute and the value is the option
     * text. (Careful on the overuse of 'value' here.)
     *
     * @param string $id           element ID, must be unique on page
     * @param string $label        text of label for the element
     * @param array  $content      options array, value => text
     * @param string $instructions instructions for valid input
     * @param string $blank_select whether to have a blank entry, default false
     * @param string $selected     selected value, default null
     *
     * @return void
     *
     * @todo add a $name parameter
     */

    function dropdown($id, $label, $content, $instructions=null,
                      $blank_select=false, $selected=null)
    {
        $this->element('label', array('for' => $id), $label);
        $this->elementStart('select', array('id' => $id, 'name' => $id));
//        if ($blank_select) {
//            $this->element('option', array('value' => ''));
//        }
        foreach ($content as $value => $option) {
            if ($value == $selected) {
                $this->element('option', array('value' => $value,
                                               'selected' => 'selected'),
                               $option);
            } else {
                $this->element('option', array('value' => $value), $option);
            }
        }
        $this->elementEnd('select');
        if ($instructions) {
            $this->element('p', 'form_guide', $instructions);
        }
    }

    /**
     * output an HTML hidden element
     *
     * $id is re-used as name
     *
     * @param string $id    element ID, must be unique on page
     * @param string $value hidden element value, default null
     * @param string $name  name, if different than ID
     *
     * @return void
     */

    function hidden($id, $value, $name=null)
    {
        $this->element('input', array('name' => ($name) ? $name : $id,
                                      'type' => 'hidden',
//                                      'id' => $id,
                                      'value' => $value));
    }

    /**
     * output an HTML password input and associated elements
     *
     * @param string $id           element ID, must be unique on page
     * @param string $label        text of label for the element
     * @param string $instructions instructions for valid input
     *
     * @return void
     *
     * @todo add a $name parameter
     */

    function password($id, $label, $instructions=null)
    {
        $this->element('label', array('for' => $id), $label);
        $attrs = array('name' => $id,
                       'type' => 'password',
                       'class' => 'password',
                       'id' => $id);
        $this->element('input', $attrs);
        if ($instructions) {
            $this->element('p', 'form_guide', $instructions);
        }
    }

    /**
     * output an HTML submit input and associated elements
     *
     * @param string $id    element ID, must be unique on page
     * @param string $label text of the button
     * @param string $cls   class of the button, default 'submit'
     * @param string $name  name, if different than ID
     *
     * @return void
     *
     * @todo add a $name parameter
     */

    function submit($id, $label, $cls='submit', $name=null, $title=null)
    {
        $this->element('input', array('type' => 'submit',
                                      'id' => $id,
                                      'name' => ($name) ? $name : $id,
                                      'class' => $cls,
                                      'value' => $label,
                                      'title' => $title));
    }

    /**
     * output a script (almost always javascript) tag
     *
     * @param string $src          relative or absolute script path
     * @param string $type         'type' attribute value of the tag
     *
     * @return void
     */
    function script($src, $type='text/javascript')
    {
        $url = parse_url($src);
        if( empty($url->scheme) && empty($url->host) && empty($url->query) && empty($url->fragment))
        {
            $src = common_path($src) . '?' . SHAISHAI_VERSION;
        }
        $this->element('script', array('type' => $type,
                                               'src' => $src),
                               ' ');
    }

    /**
     * output a css link
     *
     * @param string $src     relative path within the theme directory, or an absolute path
     * @param string $theme        'theme' that contains the stylesheet
     * @param string media         'media' attribute of the tag
     *
     * @return void
     */
    function cssLink($src,$theme=null,$media=null)
    {
        $url = parse_url($src);
        if( empty($url->scheme) && empty($url->host) && empty($url->query) && empty($url->fragment))
        {
            if(file_exists(theme_file($src,$theme))){
               $src = theme_path($src, $theme);
            }else{
               $src = common_path($src);
            }
        }
        $this->element('link', array('rel' => 'stylesheet',
                                'type' => 'text/css',
                                'href' => $src . '?' . SHAISHAI_VERSION,
                                'media' => $media));
    }

    /**
     * output an HTML textarea and associated elements
     *
     * @param string $id           element ID, must be unique on page
     * @param string $label        text of label for the element
     * @param string $content      content of the textarea, default none
     * @param string $instructions instructions for valid input
     *
     * @return void
     *
     * @todo add a $name parameter
     * @todo add a $cols parameter
     * @todo add a $rows parameter
     */

    function textarea($id, $label, $content=null, $instructions=null)
    {
        $this->element('label', array('for' => $id), $label);
        $this->element('textarea', array('rows' => 3,
                                         'cols' => 40,
                                         'name' => $id,
                                         'id' => $id),
                       ($content) ? $content : '');
        if ($instructions) {
            $this->element('p', 'form_guide', $instructions);
        }
    }
    
    function option($value, $text, $selectedValue=null) {
    	$optionattrs = array('value' => $value);
    	if ($selectedValue != null
    			&& $selectedValue == $value) {
    		$optionattrs['selected'] = 'selected';
    	}
    	$this->elementStart('option', $optionattrs);	
        $this->text($text);	
        $this->elementEnd('option');
    }
}
