<?php

return [

    // ── Common ────────────────────────────────────────────────────────────────
    'common' => [
        'app_subtitle'    => 'Financial Management',
        'save'            => 'Save',
        'cancel'          => 'Cancel',
        'new_transaction' => 'New Transaction',
        'toggle_theme'    => 'Toggle theme',
        'logout'          => 'Logout',
    ],

    // ── Navigation ────────────────────────────────────────────────────────────
    'nav' => [
        'section_main'          => 'Main',
        'dashboard'             => 'Dashboard',
        'transactions'          => 'Transactions',
        'categories'            => 'Categories',
        'section_intelligence'  => 'Intelligence',
        'ai_analysis'           => 'Smart Analysis',
        'section_account'       => 'Account',
        'settings'              => 'Settings',
    ],

    // ── Auth ──────────────────────────────────────────────────────────────────
    'auth' => [
        'subtitle'    => 'Intelligent financial management',
        'email'       => 'Email',
        'password'    => 'Password',
        'remember_me' => 'Remember me',
        'sign_in'     => 'Sign In',
        'demo_label'  => 'Demo',
    ],

    // ── Dashboard ─────────────────────────────────────────────────────────────
    'dashboard' => [
        'subtitle'              => 'Financial overview for :month',
        'monthly_expenses'      => 'Monthly Expenses',
        'monthly_income'        => 'Monthly Income',
        'monthly_balance'       => 'Monthly Balance',
        'total_balance'         => 'Total Balance',
        'monthly_evolution'     => 'Monthly Evolution',
        'expenses_vs_income'    => 'Expenses vs Income — last 6 months',
        'expenses_by_category'  => 'Expenses by Category',
        'current_month'         => 'Current month',
        'no_expenses_month'     => 'No expenses recorded this month.',
        'top_impact'            => 'Top Impact',
        'top_category_label'    => 'top spending category',
        'no_expenses'           => 'No expenses recorded.',
        'recent_transactions'   => 'Recent Transactions',
        'view_all'              => 'View all',
        'no_transactions'       => 'No transactions recorded.',
        'chart_expenses'        => 'Expenses',
        'chart_income'          => 'Income',
    ],

    // ── Transactions ──────────────────────────────────────────────────────────
    'transactions' => [
        'page_title'          => 'Transactions',
        'page_subtitle'       => 'Complete history of expenses and income',
        'search_placeholder'  => 'Search by description...',
        'all_types'           => 'All types',
        'expenses'            => 'Expenses',
        'income'              => 'Income',
        'all_categories'      => 'All categories',
        'all_months'          => 'All months',
        'all_years'           => 'All years',
        'filter'              => 'Filter',
        'clear'               => 'Clear',
        'results_label'       => 'transactions found',
        'new_transaction'     => 'New Transaction',
        'no_found'            => 'No transactions found.',
        'add_first'           => 'Add first transaction',
        'col_description'     => 'Description',
        'col_category'        => 'Category',
        'col_date'            => 'Date',
        'col_type'            => 'Type',
        'col_amount'          => 'Amount',
        'remove_confirm'      => 'Remove this transaction?',
        'expense_label'       => 'Expense',
        'income_label'        => 'Income',
    ],

    // ── Create / Edit Transaction ─────────────────────────────────────────────
    'create_transaction' => [
        'page_title'              => 'New Transaction',
        'page_subtitle'           => 'Record an expense or income',
        'type_label'              => 'Type *',
        'amount_label'            => 'Amount *',
        'category_label'          => 'Category *',
        'select_category'         => 'Select a category',
        'description_label'       => 'Description *',
        'description_placeholder' => 'E.g.: Supermarket, Monthly salary...',
        'date_label'              => 'Date *',
        'save_btn'                => 'Save Transaction',
    ],

    'edit_transaction' => [
        'page_title'    => 'Edit Transaction',
        'page_subtitle' => 'Update transaction data',
        'update_btn'    => 'Update Transaction',
    ],

    // ── Categories ────────────────────────────────────────────────────────────
    'categories' => [
        'page_title'        => 'Categories',
        'page_subtitle'     => 'Manage transaction categories',
        'new_category'      => 'New Category',
        'default_badge'     => 'default',
        'remove_confirm'    => 'Remove category :name?',
        'no_categories'     => 'No categories found.',
        'transaction_count' => ':count transaction|:count transactions',
    ],

    // ── Create Category ───────────────────────────────────────────────────────
    'create_category' => [
        'page_title'       => 'New Category',
        'page_subtitle'    => 'Create a custom category',
        'name_label'       => 'Name *',
        'name_placeholder' => 'E.g.: Travel, Pets...',
        'color_label'      => 'Color *',
        'icon_label'       => 'Icon (name) *',
        'icon_placeholder' => 'tag, home, star...',
        'icon_help'        => 'Icon identifier for future use.',
        'create_btn'       => 'Create Category',
    ],

    // ── Settings ──────────────────────────────────────────────────────────────
    'settings' => [
        'page_title'       => 'Settings',
        'page_subtitle'    => 'Manage your integrations and preferences.',
        'ai_title'         => 'AI Analysis',
        'ai_desc'          => 'Configure your OpenAI key to use intelligent financial analysis.',
        'openai_key_label' => 'OpenAI API Key',
        'key_configured'   => 'Key configured. Fill below to replace.',
        'key_info'         => 'Your key is stored encrypted. Get yours at',
        'remove_key'       => 'Remove existing key',
        'save'             => 'Save',
        'saved'            => 'Settings saved successfully!',
        'lang_title'       => 'Language',
        'lang_desc'        => 'Choose the interface language.',
        'lang_label'       => 'Language',
        'lang_pt'          => 'Português',
        'lang_en'          => 'English',
        'lang_es'          => 'Español',
    ],

    // ── AI Analysis ───────────────────────────────────────────────────────────
    'ai' => [
        'page_title'         => 'Smart Analysis',
        'page_subtitle'      => 'AI-generated financial diagnosis',
        'card_title'         => 'AI Financial Advisor',
        'card_desc'          => 'Strategic analysis based on your transactions from the last 6 months. The system sends your financial data to OpenAI and returns personalized insights.',
        'api_not_configured' => 'API Key not configured',
        'api_config_hint'    => 'To activate smart analysis, add your OpenAI key in',
        'settings_link'      => 'Settings',
        'generate_btn'       => 'Generate Financial Analysis',
        'analyzing'          => 'Analyzing your data...',
        'analysis_generated' => 'Analysis generated on',
    ],

    // ── Flash / Controller messages ───────────────────────────────────────────
    'messages' => [
        'transaction_stored'         => 'Transaction recorded successfully!',
        'transaction_updated'        => 'Transaction updated successfully!',
        'transaction_deleted'        => 'Transaction removed successfully!',
        'category_created'           => 'Category created successfully!',
        'category_default_error'     => 'Default categories cannot be removed.',
        'category_has_transactions'  => 'Cannot remove a category with linked transactions.',
        'category_deleted'           => 'Category removed successfully!',
        'key_too_long'               => 'The key is too long.',
        'api_not_configured'         => 'OpenAI API Key not configured. Add your key in settings.',
        'openai_error'               => 'Failed to connect to OpenAI. Check your API Key in settings.',
        'auth_email_required'        => 'Email is required.',
        'auth_email_invalid'         => 'Enter a valid email.',
        'auth_password_required'     => 'Password is required.',
        'auth_invalid_credentials'   => 'Invalid credentials. Please try again.',
        'unauthorized'               => 'Unauthorized access.',
    ],

];
