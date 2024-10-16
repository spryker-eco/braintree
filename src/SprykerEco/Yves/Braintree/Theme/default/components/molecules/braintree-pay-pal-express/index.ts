import register from 'ShopUi/app/registry';
export default register('braintree-pay-pal-express', () => import(
    /* webpackMode: "lazy" */
    /* webpackChunkName: "braintree-pay-pal-express" */
    './braintree-pay-pal-express'));
