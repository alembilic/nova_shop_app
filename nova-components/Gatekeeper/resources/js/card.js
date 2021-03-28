import Multiselect from 'vue-multiselect'

Nova.booting((Vue, router, store) => {
    Vue.component('multiselect', Multiselect)

  Vue.component('gatekeeper', require('./components/Card'))
})
