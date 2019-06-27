import register from 'ShopUi/app/registry';
export default register('payment-form', () => import(/* webpackMode: "lazy" */'./payment-form'));

