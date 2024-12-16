<?php
include_once "turnslite.php";

class PostForm
{
	private $buffer = "";
	private $hidden_data = array();

	public function __construct($form_action, $action_method)
	{
		$this->buffer .= "<form action='$form_action' method=$action_method enctype=multipart/form-data>";
		$this->buffer .= "<table>";
	}

	public function add_text_field($label, $name, $value = "")
	{
		$this->buffer .= "<tr>";
		$this->buffer .= "<th>$label</th>";
		$this->buffer .= "<td><input type=text name=$name value='$value'></td>";
		$this->buffer .= "</tr>";

		return $this;
	}

	public function add_checkbox($label, $name, $selected = false)
	{
		$this->buffer .= "<tr>";
		$this->buffer .= "<th>$label</th>";

		if ($selected)
			$this->buffer .= "<td><input type=checkbox name=$name checked></td>";
		else
			$this->buffer .= "<td><input type=checkbox name=$name></td>";

		$this->buffer .= "</tr>";

		return $this;
	}

	public function add_number($label, $name, $value = "")
	{
		$this->buffer .= "<tr>";
		$this->buffer .= "<th>$label</th>";
		$this->buffer .= "<td><input type=number name=$name value='$value'></td>";
		$this->buffer .= "</tr>";

		return $this;
	}

	public function add_password_field($label, $name, $value = "")
	{
		$this->buffer .= "<tr>";
		$this->buffer .= "<th>$label</th>";
		$this->buffer .= "<td><input type=password name=$name value='$value'></td>";
		$this->buffer .= "</tr>";

		return $this;
	}

	public function add_text_area($label, $name, $value="", $id="")
	{
		$this->buffer .= "<tr>";
		$this->buffer .= "<th>$label</th>";
		$this->buffer .= "<td><textarea id='$id' name='$name'>$value</textarea>";
		$this->buffer .= "</tr>";

		return $this;
	}

	public function add_file($label, $name)
	{
		$max_upload = ini_get("upload_max_filesize");
		$this->buffer .= "<tr>";
		$this->buffer .= "<th>$label</th>";
		$this->buffer .= "<td><div class=file_upload_row><input class=file_upload type=file multiple name=$name><button type=button class=clear_file onclick=clear_file_upload()>Clear</button></div><p class=max_upload>Max size: $max_upload</p></td>";
		$this->buffer .= "</tr>";

		return $this;
	}

	public function add_captcha($label, $name)
	{
		if (!turnslite_is_enabled())
			return $this;

		$this->buffer .= "<tr>";
		$this->buffer .= "<th>$label</th>";
		$this->buffer .= "<td><div class='cf-turnstile' data-sitekey='" . turnslite_get_site_key() . "'></div><noscript class=no_js_warning><p>Turnslite requires JavaScript to be enabled</p></noscript></td>";
		$this->buffer .= "<tr>";
		return $this;
	}

	public function add_hidden_data($name, $value)
	{
		$this->hidden_data[$name] = $value;
		return $this;
	}

	public function add_dropdown($label, $name, $values, $selected="")
	{
		$this->buffer .= "<tr>";
		$this->buffer .= "<th>$label</th>";
		$this->buffer .= "<td><select name=$name>";
		
		foreach ($values as $value)
		{
			if ($value == $selected)
				$this->buffer .= "<option value=$value selected>$value</option>";
			else
				$this->buffer .= "<option value=$value>$value</option>";
		}

		$this->buffer .= "</select></td></tr>";

		return $this;
	}

	public function add_checkboxes($label, $boxes)
	{
		$this->buffer .= "<tr><th>$label</th><td class=checkboxes>";
		
		foreach ($boxes as $box_label => $box_name)
		{
			$this->buffer .= "<input class=checkbox id=$box_name type=checkbox name=$box_name><label for=$box_name>$box_label</label>";
		}

		$this->buffer .= "</td></tr>";

		return $this;
	}

	public function finalize()
	{
		$this->buffer .= "</table>";
		$this->buffer .= "<button type=submit>Submit</button>";
	
		// add hidden data to the form
		foreach ($this->hidden_data as $key => $value)
		{
			$this->buffer .= "<input type=hidden name=$key value='$value'>";
		}

		$this->buffer .= "</form>";
		echo $this->buffer;
	}
}

class ActionLink
{
	private $buffer = "";
	private $label = "";
	private $name = "";

	public function __construct($action, $name, $label, $method="POST")
	{
		$this->label = $label;
		$this->name = $name;
		$this->buffer .= "<form method=$method action='$action' id=$name>";
	}

	public function finalize()
	{
		$this->buffer .= "<a href=# onclick=\"document.forms['$this->name'].submit();\">$this->label</a>";
		$this->buffer .= "</form>";

		echo $this->buffer;
	}

	public function add_data($name, $value)
	{
		$this->buffer .= "<input type=hidden name=$name value='$value'>";
		return $this;
	}
}

function parameter_link($page, $arguments)
{
	$link = "$page?";

	foreach ($arguments as $key => $value)
	{
		$link .= $key . "=" . urlencode($value) ."&";
	}

	return $link;
}

function display_parameter_link($name, $page, $arguments, $class = "", $id = "")
{
	$href = parameter_link($page, $arguments);
	echo "<a href=\"$href\" class=$class>$name</a>";
}