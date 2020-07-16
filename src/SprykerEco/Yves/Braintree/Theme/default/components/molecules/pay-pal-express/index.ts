import register from 'ShopUi/app/registry';
export default register('pay-pal-express', () => import(
    /* webpackMode: "lazy" */
    /* webpackChunkName: "pay-pal-express" */
    './pay-pal-express'));
