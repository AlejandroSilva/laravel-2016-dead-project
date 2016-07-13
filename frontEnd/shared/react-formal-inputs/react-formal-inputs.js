var React = require('react')
    , widgets = require('react-widgets');

function wrapWithDefaults(Component, defaults){
    return React.createClass({
        getDefaultProps: function(){ return defaults },
        render: function(){
            return React.createElement(Component, this.props)
        }

        //cuando se utiliza wrapWithDefault se pierde el focus del elemento....
        // , focus: function(){
        //     this.refs.referencia.focus()
        // }
    })
}

var types = Object.create(null);


types.combobox       = widgets.Combobox
types.dropdownlist   = widgets.DropdownList
types.calendar       = widgets.Calendar
types.selectlist     = widgets.SelectList

types.date           =
   types.datepicker   = wrapWithDefaults(widgets.DateTimePicker, { time: false /*, ref: 'referencia'*/})
types.date2   = widgets.DateTimePicker // FIX!!

types.time           =
    types.timepicker   = wrapWithDefaults(widgets.DateTimePicker, { calendar: false })

types.datetime =
    types['datetime-local'] =
        types.datetimepicker  = widgets.DateTimePicker

types.number         =
    types.numberpicker = widgets.NumberPicker

types.array          =
    types.multiselect  = widgets.Multiselect

//types.date2 = ()=> <input type="date" placeholder="fecha"/>

module.exports = types