import register from 'ShopUi/app/registry';
export default register('braintree-payment-form', () => import(
    /* webpackMode: "lazy" */
    /* webpackChunkName: "braintree-payment-form" */
    './braintree-payment-form'));
