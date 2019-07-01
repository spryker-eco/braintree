import register from 'ShopUi/app/registry';
export default register('pay-pal-express', () => import(/* webpackMode: "lazy" */'./pay-pal-express')
);
