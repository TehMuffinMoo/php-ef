<?php
class Plugins {
    private $api;
    private $core;
    private $db;

    public function __construct($api,$core,$db) {
        $this->api = $api;
        $this->core = $core;
        $this->db = $db;
    }

    public function getInstalledPlugins() {
        $list = [];
        foreach ($GLOBALS['plugins'] as $key => $value) {
            $list[] = $value;
        }
        return $list;
    }

    public function getOnlinePlugins() {
        $installedPlugins = $this->getInstalledPlugins();
        $list = [
            'https://github.com/TehMuffinMoo/php-ef-plugin-ib',
            'https://github.com/TehMuffinMoo/php-ef-plugin-cmdb'
        ];
        $results = [];
        foreach ($list as $l) {
            $ls = explode('https://github.com/',$l);
            $results[] = $this->api->query->get('https://raw.githubusercontent.com/'.$ls[1].'/refs/heads/main/plugin.json');
        }
        return $results;
    }

    public function getPluginRepositories() {
        return $this->core->config->get('PluginRepositories');
    }

    public function setPluginRepositories($list) {
        $Arr = array(
            'PluginRepositories' => $list
        );
        $Config = $this->core->config->get();
        return $this->core->config->set($Config,$Arr);
    }

    public function getAvailablePlugins() {
        $installedPlugins = $this->getInstalledPlugins();
        $onlinePlugins = $this->getOnlinePlugins();
        $allPlugins = array_merge($onlinePlugins, $installedPlugins);
    
        // Flatten the array if there are nested arrays
        $flattenedPlugins = [];
        foreach ($allPlugins as $plugin) {
            if (is_array($plugin) && isset($plugin['name'])) {
                $flattenedPlugins[] = $plugin;
            } elseif (is_array($plugin) && isset($plugin[0]['name'])) {
                $flattenedPlugins[] = $plugin[0];
            }
        }
    
        // Remove duplicates based on 'name' and mark status and source
        $uniquePlugins = [];
        $installedPluginNames = array_column($installedPlugins, 'name');
        $onlinePluginNames = array_column(array_merge(...$onlinePlugins), 'name'); // Flatten online plugins
    
        foreach ($flattenedPlugins as $plugin) {
            if (!isset($uniquePlugins[$plugin['name']])) {
                $plugin['status'] = in_array($plugin['name'], $installedPluginNames) ? 'Installed' : 'Available';
                if (in_array($plugin['name'], $installedPluginNames) && in_array($plugin['name'], $onlinePluginNames)) {
                    $plugin['source'] = 'Online';
                    // Merge online and local plugin details
                    $onlinePlugin = current(array_filter($onlinePlugins, function($p) use ($plugin) {
                        return isset($p[0]['name']) && $p[0]['name'] === $plugin['name'];
                    }));
                    $plugin = array_merge($plugin, $onlinePlugin[0]);
                } elseif (in_array($plugin['name'], $installedPluginNames)) {
                    $plugin['source'] = 'Local';
                } else {
                    $plugin['source'] = 'Online';
                }
                $uniquePlugins[$plugin['name']] = $plugin;
            } else {
                // Merge details if the plugin is already in the uniquePlugins array
                $uniquePlugins[$plugin['name']] = array_merge($uniquePlugins[$plugin['name']], $plugin);
            }
        }
    
        // Convert back to a list
        $result = array_values($uniquePlugins);
    
        return $result;
    }
}