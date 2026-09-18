import React from 'react';
import { createRoot } from 'react-dom/client';
import 'bootstrap';

// Client Components (TailwindCSS v4)
import ChatbotWidget from './components/client/ChatbotWidget';
import ShopApp from './components/client/ShopApp';
import DishDetailApp from './components/client/DishDetailApp';
import CartCheckoutApp from './components/client/CartCheckoutApp';
import OrderHistoryApp from './components/client/OrderHistoryApp';
import TrackOrderApp from './components/client/TrackOrderApp';
import GroupOrderApp from './components/client/GroupOrderApp';
import SubscriptionApp from './components/client/SubscriptionApp';
import RefundApp from './components/client/RefundApp';
import AuthApp from './components/client/AuthApp';

// Admin Components (Bootstrap 5)
import AdminDashboardApp from './components/admin/AdminDashboardApp';
import AdminOrdersApp from './components/admin/AdminOrdersApp';
import AdminDishesApp from './components/admin/AdminDishesApp';
import AdminCategoriesApp from './components/admin/AdminCategoriesApp';
import AdminPackagesApp from './components/admin/AdminPackagesApp';
import AdminSubscriptionsApp from './components/admin/AdminSubscriptionsApp';
import AdminUsersApp from './components/admin/AdminUsersApp';
import AdminRefundsApp from './components/admin/AdminRefundsApp';
import AdminCouponsApp from './components/admin/AdminCouponsApp';
import AdminKitchenApp from './components/admin/AdminKitchenApp';
import CustomerBackupsApp from './components/admin/CustomerBackupsApp';
import StaffWorkspaceApp from './components/admin/StaffWorkspaceApp';
import AdminSettingsApp from './components/admin/AdminSettingsApp';

// Registry of all React apps
const components = {
    // Client
    ChatbotWidget,
    ShopApp,
    DishDetailApp,
    CartCheckoutApp,
    OrderHistoryApp,
    TrackOrderApp,
    GroupOrderApp,
    SubscriptionApp,
    RefundApp,
    AuthApp,

    // Admin
    AdminDashboardApp,
    AdminOrdersApp,
    AdminDishesApp,
    AdminCategoriesApp,
    AdminPackagesApp,
    AdminSubscriptionsApp,
    AdminUsersApp,
    AdminRefundsApp,
    AdminCouponsApp,
    AdminKitchenApp,
    CustomerBackupsApp,
    StaffWorkspaceApp,
    AdminSettingsApp,
};

function mountReactComponents() {
    const mountNodes = document.querySelectorAll('[data-react-component]');
    mountNodes.forEach((node) => {
        // Prevent double mounting
        if (node.dataset.reactMounted === 'true') return;

        const componentName = node.getAttribute('data-react-component');
        const Component = components[componentName];

        if (!Component) {
            console.warn(`[React Mount] Component "${componentName}" not found in registry.`);
            return;
        }

        let props = {};
        const rawProps = node.getAttribute('data-props');
        if (rawProps) {
            try {
                props = JSON.parse(rawProps);
            } catch (err) {
                console.error(`[React Mount] Failed to parse data-props for ${componentName}:`, err);
            }
        }

        try {
            const root = createRoot(node);
            root.render(<Component {...props} />);
            node.dataset.reactMounted = 'true';
        } catch (err) {
            console.error(`[React Mount] Error rendering ${componentName}:`, err);
        }
    });
}

// Automatically mount when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountReactComponents);
} else {
    mountReactComponents();
}

// Global hook for dynamic client updates
window.mountReactComponents = mountReactComponents;
