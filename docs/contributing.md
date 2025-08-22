# 贡献指南

感谢您对飞书 SDK 的关注！我们欢迎所有形式的贡献，包括但不限于：

- 🐛 报告 Bug
- 💡 提出新功能建议
- 📝 改进文档
- 🔧 提交代码修复
- 🧪 添加测试用例

## 开发环境设置

### 系统要求

- PHP 8.1 或更高版本
- Composer
- Git

### 克隆项目

```bash
git clone https://github.com/zhaiyuxin103/Feishu.git
cd Feishu
```

### 安装依赖

```bash
composer install
```

### 运行测试

```bash
composer test
```

### 代码检查

```bash
composer lint
```

## 开发流程

### 1. 创建分支

```bash
git checkout -b feature/your-feature-name
# 或者
git checkout -b fix/your-bug-fix
```

### 2. 编写代码

请遵循以下编码规范：

- 使用 PSR-12 编码标准
- 添加适当的类型声明
- 编写清晰的注释
- 确保代码通过所有测试

### 3. 运行测试

```bash
# 运行所有测试
composer test

# 运行特定测试
./vendor/bin/pest tests/Unit/MessageTest.php

# 生成测试覆盖率报告
./vendor/bin/pest --coverage
```

### 4. 代码检查

```bash
# 代码格式检查
composer lint

# 静态分析
./vendor/bin/phpstan analyse
```

### 5. 提交代码

```bash
git add .
git commit -m "feat: add new feature description"
git push origin feature/your-feature-name
```

### 6. 创建 Pull Request

在 GitHub 上创建 Pull Request，并包含以下信息：

- 清晰的标题和描述
- 相关的 Issue 链接
- 测试结果
- 代码变更说明

## 编码规范

### PHP 代码规范

- 遵循 PSR-12 编码标准
- 使用严格类型声明 `declare(strict_types=1);`
- 类名使用 PascalCase
- 方法名使用 camelCase
- 常量使用 UPPER_SNAKE_CASE

### 文档规范

- 使用中文编写文档
- 代码示例要完整可运行
- 添加适当的注释和说明

### 测试规范

- 每个新功能都要有对应的测试
- 测试覆盖率不低于 80%
- 测试名称要清晰描述测试内容

## 提交信息规范

我们使用 [Conventional Commits](https://www.conventionalcommits.org/) 规范：

```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

### 类型说明

- `feat`: 新功能
- `fix`: 修复 Bug
- `docs`: 文档更新
- `style`: 代码格式调整
- `refactor`: 代码重构
- `test`: 测试相关
- `chore`: 构建过程或辅助工具的变动

### 示例

```
feat: add support for image messages

- Add MessageTypeEnum::Image case
- Implement image message sending functionality
- Add corresponding tests

Closes #123
```

## Issue 报告

### Bug 报告

请包含以下信息：

- 操作系统和 PHP 版本
- SDK 版本
- 错误信息和堆栈跟踪
- 重现步骤
- 期望行为

### 功能请求

请包含以下信息：

- 功能描述
- 使用场景
- 预期 API 设计
- 相关参考

## 发布流程

### 版本号规范

我们使用 [Semantic Versioning](https://semver.org/)：

- `MAJOR.MINOR.PATCH`
- `MAJOR`: 不兼容的 API 修改
- `MINOR`: 向下兼容的功能性新增
- `PATCH`: 向下兼容的问题修正

### 发布步骤

1. 更新版本号
2. 更新 CHANGELOG.md
3. 创建 Git 标签
4. 发布到 Packagist

## 联系方式

如果您有任何问题或建议，请通过以下方式联系我们：

- GitHub Issues: [https://github.com/zhaiyuxin103/Feishu/issues](https://github.com/zhaiyuxin103/Feishu/issues)
- Email: zhaiyuxin103@hotmail.com

## 致谢

感谢所有为这个项目做出贡献的开发者！

---

**注意**: 在提交代码之前，请确保您已经阅读并同意我们的 [行为准则](CODE_OF_CONDUCT.md)。
