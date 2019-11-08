YUI.add('moodle-tool_editrolesbycap-capabilityformfield', function (Y, NAME) {

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Auto-save functionality for during quiz attempts.
 *
 * @module moodle-tool_editrolesbycap-capabilityformfield
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * YUI module to add filtering to the capability form field type.
 *
 * @class M.tool_editrolesbycap
 */

var MODULENAME = function() {
    MODULENAME.superclass.constructor.apply(this, arguments);
};
Y.extend(MODULENAME, Y.Base, {
    initializer: function() {
    }
}, {
    NAME: 'capabilityformfield',
    ATTRS: {}
});

M.tool_editrolesbycap = M.tool_editrolesbycap || {};

M.tool_editrolesbycap.init_capabilityformfield = function(selector) {
    this.select = Y.one(selector);
    if (!this.select) {
        return;
    }

    // Get any existing filter value.
    var filtervalue = this.get_filter_cookie();

    // Create a div to hold the search UI.
    this.div = Y.Node.create('<div class="capabilitysearchui form-inline m-t-1"></div>').setStyles({
        width: this.select.get('offsetWidth'),
        marginLeft: 'auto',
        marginRight: 'auto'
    });
    // Create the capability search input.
    this.input = Y.Node.create('<input type="text" id="' + this.select.get('id') +
            'capabilitysearch" class="form-control" value="' + filtervalue + '" />');
    // Create a label for the search input.
    this.label = Y.Node.create('<label for="' + this.input.get('id') + '">' +
            M.util.get_string('filter', 'moodle') + ' </label>');
    // Create a clear button to clear the input.
    this.button = Y.Node.create('<input type="button" class="btn btn-secondary" value="' + M.util.get_string('clear', 'moodle') +
            '" />').set('disabled', filtervalue === '');

    // Tie it all together.
    this.div.append(this.label).append(this.input).append(this.button);

    // Insert it into the div.
    this.select.ancestor().append(this.div);

    this.nonemessage = Y.Node.create('<optgroup label="' +
            M.util.get_string('nonematch', 'tool_editrolesbycap') + '"></optgroup>');
    this.select.append(this.nonemessage);
    this.set_visible(this.nonemessage, false);

    // Wire the events so it actually does something.
    this.input.on('keyup', this.change, this);
    this.button.on('click', this.clear, this);

    if (filtervalue !== '') {
        this.filter();
    }

    MODULENAME({});
};

/**
 * Sets a cookie that describes the filter value.
 * The cookie stores the context, and the time it was created and upon
 * retrieval is checked to ensure that the cookie is for the correct
 * context and is no more than an hour old.
 *
 * @param {String} value the value to store in the cookie.
 */
M.tool_editrolesbycap.set_filter_cookie = function(value) {
    var cookie = {
        flttime: new Date().getTime(),
        fltvalue: value
    };
    Y.Cookie.setSubs("captblflt", cookie);
};

/**
 * Gets the existing filter value if there is one.
 * The cookie stores the context, and the time it was created and upon
 * retrieval is checked to ensure that the cookie is for the correct
 * context and is no more than an hour old.
 *
 * @return {String} value the value from the cookie.
 */
M.tool_editrolesbycap.get_filter_cookie = function() {
    var cookie = Y.Cookie.getSubs('captblflt');
    if (cookie !== null && parseInt(cookie.flttime, 10) > new Date().getTime() - (60 * 60 * 1000)) {
        return cookie.fltvalue;
    }
    return '';
};

/**
 * Clears the filter value.
 */
M.tool_editrolesbycap.clear = function() {
    this.input.set('value', '');
    if (this.delayhandle !== -1) {
        clearTimeout(this.delayhandle);
        this.delayhandle = -1;
    }
    this.filter();
};

/**
 * Event callback for when the filter value changes
 */
M.tool_editrolesbycap.change = function() {
    var self = this;
    var handle = setTimeout(function() {
            self.filter();
        }, this.searchdelay);
    if (this.delayhandle !== -1) {
        clearTimeout(this.delayhandle);
    }
    this.delayhandle = handle;
};

M.tool_editrolesbycap.set_visible = function(element, visible) {
    if (!Y.one('body.ie') && !Y.one('body.safari')) {
        if (visible) {
            element.setStyle('display', 'block');
        } else {
            element.setStyle('display', 'none');
        }
    } else {
        // This is a deeply evil hack to make the filtering work in IE.
        // IE ignores display: none; on select options, but wrapping the
        // option in a span does seem to hide the option.
        // Thanks http://work.arounds.org/issue/96/option-elements-do-not-hide-in-IE/.
        if (visible) {
            if (element.get('parentNode').test('span')) {
                element.unwrap();
            }
        } else {
            if (!element.get('parentNode').test('span')) {
                element.wrap('<span style="display: none;"/>');
            }
        }
    }
};

/**
 * Filters the capability selector
 */
M.tool_editrolesbycap.filter = function() {
    var filtertext = this.input.get('value').toLowerCase();

    this.set_filter_cookie(filtertext);

    this.button.set('disabled', (filtertext === ''));

    var allhidden = true;
    this.select.all('optgroup').each(function(optgroup) {
        this.set_visible(optgroup, false);
        var lastgroup = optgroup;

        optgroup.all('option').each(function(option) {
            var capname = option.get('text').toLowerCase();
            if (capname.indexOf(filtertext) >= 0) {
                this.set_visible(lastgroup, true);
                this.set_visible(option, true);
                allhidden = false;
            } else {
                this.set_visible(option, false);
            }
        }, this);
    }, this);
    if (allhidden) {
        this.set_visible(this.nonemessage, true);
    }
};


}, '@VERSION@', {"requires": ["base", "dom", "event", "cookie"]});
