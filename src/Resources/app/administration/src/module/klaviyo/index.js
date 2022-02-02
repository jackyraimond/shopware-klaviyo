import './page/klaviyo-job-listing';
import './page/klaviyo-integration-settings';

import enGB from './snippet/en-GB.json';

const {Module} = Shopware;

Module.register('klaviyo-plugin', {
    type: 'plugin',
    title: 'klaviyo-job-listing.general.title',
    description: 'klaviyo-job-listing.general.description',
    color: '#F88962',
    icon: 'default-avatar-multiple',

    snippets: {
        'en-GB': enGB,
    },

    routes: {
        index: {
            component: 'klaviyo-job-listing',
            path: 'index'
        },
        settings: {
            component: 'klaviyo-integration-settings',
            path: 'settings',
            meta: {
                parentPath: 'sw.settings.index.plugins'
            }
        }
    },

    settingsItem: {
        group: 'plugins',
        to: 'klaviyo.plugin.settings',
        icon: 'default-object-rocket',
        label: 'klaviyo-integration-settings.label'
    },

    navigation: [
        {
            id: 'klaviyo',
            label: 'klaviyo-job-listing.menu.title',
            color: '#F88962',
            icon: 'default-avatar-multiple',
            parent: 'sw-marketing',
            path: 'klaviyo.plugin.index',
            position: 100
        }
    ],
})
