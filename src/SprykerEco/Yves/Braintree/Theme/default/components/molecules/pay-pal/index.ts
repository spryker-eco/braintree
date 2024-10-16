import register from 'ShopUi/app/registry';
export default register('pay-pal', () => import(
    /* webpackMode: "lazy" */
    /* webpackChunkName: "pay-pal" */
    './pay-pal'));
