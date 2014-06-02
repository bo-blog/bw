<?php 
// This is a demo of the plugin mechanism
// Security check.
if (!defined ('P')) {
	die ('Access Denied.');
} 
// Class name: ext_XXXX
class ext_hello_world {
	private static $ln;

	/**
	* Use the init to load languages, etc.
	* Must have.
	*/
	public static function init ()
	{
		self :: $ln = bw :: $conf['siteLang'] == 'zh-cn' ? '你好，世界！' : 'Hello, world!';
	} 

	/**
	* Function setup() will be loaded when the plugin is installed.
	* Use this to initialize some data, create tables, etc.
	*/
	public static function setup ()
	{
	} 

	/**
	* Function xyz() will be loaded when the hook xyz is being executed.
	* xyz is the name of the hook.
	* The complete format is ext_ExtensionID::HookName()
	*/
	public static function header ()
	{
		return '<div>' . self :: $ln . '</div>';
	} 

	public static function footer ()
	{
		return '<div>Hello again, World!</div>';
	} 

	public static function textParser ($text)
	{
		return $text . '<div>I Love the World!</div>';
	} 

	public static function generateOutputDone ($objView)
	{
		$objView -> outputContent = str_replace ('</body>', '<div>Do you love the world?</div></body>', $objView -> outputContent);
	} 

	/**
	* Function uninstall() will be loaded when the plugin is removed.
	* Use this to destroy some data, delete tables, etc.
	*/
	public static function uninstall ()
	{
	} 
} 
