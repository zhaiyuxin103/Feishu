import { defineConfig } from 'vitepress';

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: '飞书 SDK',
  description:
    '一个简单易用的 PHP 飞书 SDK，支持发送消息、管理群组和用户等功能',
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: [{ text: '指南', link: '/guide' }],

    sidebar: [
      {
        text: '快速开始',
        items: [
          { text: '使用指南', link: '/guide' },
          { text: '常见问题', link: '/faq' },
        ],
      },
      {
        text: 'API 文档',
        items: [
          { text: 'API 参考', link: '/api-reference' },
          { text: '示例代码', link: '/examples' },
          { text: 'API 示例', link: '/api-examples' },
        ],
      },
      {
        text: '集成指南',
        items: [
          { text: 'Laravel 集成', link: '/laravel-integration' },
          { text: '配置指南', link: '/configuration' },
        ],
      },
      {
        text: '开发指南',
        items: [
          { text: '贡献指南', link: '/contributing' },
          { text: '行为准则', link: '/CODE_OF_CONDUCT' },
          { text: '更新日志', link: '/changelog' },
        ],
      },
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/zhaiyuxin103/Feishu' },
    ],

    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2024-present YuXin Zhai',
    },

    search: {
      provider: 'local',
    },
  },
});
