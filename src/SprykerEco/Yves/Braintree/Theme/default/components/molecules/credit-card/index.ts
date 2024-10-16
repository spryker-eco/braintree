import './credit-card.scss';
import register from 'ShopUi/app/registry';
export default register('credit-card', () => import(
    /* webpackMode: "lazy" */
    /* webpackChunkName: "credit-card" */
    './credit-card'));
