import register from 'ShopUi/app/registry';
export default register('payment-form', () => import(
    /* webpackMode: "lazy" */
    /* webpackChunkName: "payment-form" */
    './payment-form'));
