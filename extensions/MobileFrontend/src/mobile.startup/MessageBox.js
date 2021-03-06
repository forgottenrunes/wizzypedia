var
	View = require( './View' ),
	util = require( './util' ),
	mfExtend = require( './mfExtend' );

/**
 * @class MessageBox
 * @extends View
 */
function MessageBox() {
	View.apply( this, arguments );
}

mfExtend( MessageBox, View, {
	/**
	 * @inheritdoc
	 * @memberof MessageBox
	 * @instance
	 */
	isTemplateMode: true,
	/**
	 * @memberof MessageBox
	 * @instance
	 * @mixes View#defaults
	 * @property {Object} defaults Default options hash.
	 * @property {string} [defaults.heading] heading to show along with message (text)
	 * @property {string} defaults.msg message to show (html)
	 * @property {string} defaults.className either mw-message-box-error,
	 *   mw-message-box-notice or mw-message-box-warning
	 */
	defaults: {},
	/**
	 * @memberof MessageBox
	 * @instance
	 */
	template: util.template( `
<div class="{{className}} mw-message-box">
	{{#heading}}<h2>{{heading}}</h2>{{/heading}}
	{{{msg}}}
</div>
	` )
} );

module.exports = MessageBox;
