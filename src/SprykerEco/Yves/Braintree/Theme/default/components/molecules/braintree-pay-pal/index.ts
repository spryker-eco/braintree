import register from 'ShopUi/app/registry';
export default register('braintree-pay-pal', () => import(/* webpackMode: "lazy" */'./braintree-pay-pal'));
