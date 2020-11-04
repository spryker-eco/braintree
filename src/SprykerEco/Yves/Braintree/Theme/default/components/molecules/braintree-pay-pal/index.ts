import register from 'ShopUi/app/registry';
export default register('braintree-pay-pal', () => import(
    /* webpackMode: "lazy" */
    /* webpackChunkName: "braintree-pay-pal" */
    './braintree-pay-pal'));
