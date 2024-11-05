import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('detail-laravel-nova-json-field', DetailField)
  app.component('form-laravel-nova-json-field', FormField)
})
