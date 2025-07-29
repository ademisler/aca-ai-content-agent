import React, { useState, useEffect } from 'react';
import { BarChart3, TrendingUp, Users, Zap, AlertTriangle, CheckCircle } from 'lucide-react';

interface MetricsData {
  technical: {
    bundle_size: number;
    load_time: number;
    memory_usage: number;
    error_rate: number;
    status: 'healthy' | 'warning' | 'error';
  };
  user: {
    active_today: number;
    active_week: number;
    retention_rate: number;
    satisfaction: number;
    status: 'healthy' | 'warning' | 'error';
  };
  business: {
    content_generated: number;
    conversion_rate: number;
    feature_adoption: number;
    revenue_impact: number;
    status: 'healthy' | 'warning' | 'error';
  };
  realtime: {
    [key: string]: Array<{
      data: any;
      timestamp: number;
    }>;
  };
}

interface MetricCardProps {
  title: string;
  value: string | number;
  change?: number;
  icon: React.ReactNode;
  status: 'healthy' | 'warning' | 'error';
  description?: string;
}

const MetricCard: React.FC<MetricCardProps> = ({ 
  title, 
  value, 
  change, 
  icon, 
  status, 
  description 
}) => {
  const getStatusColor = (status: string) => {
    switch (status) {
      case 'healthy': return 'text-green-600 bg-green-50 border-green-200';
      case 'warning': return 'text-yellow-600 bg-yellow-50 border-yellow-200';
      case 'error': return 'text-red-600 bg-red-50 border-red-200';
      default: return 'text-gray-600 bg-gray-50 border-gray-200';
    }
  };

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'healthy': return <CheckCircle className="w-4 h-4 text-green-600" />;
      case 'warning': return <AlertTriangle className="w-4 h-4 text-yellow-600" />;
      case 'error': return <AlertTriangle className="w-4 h-4 text-red-600" />;
      default: return null;
    }
  };

  return (
    <div className={`p-6 rounded-lg border-2 ${getStatusColor(status)}`}>
      <div className="flex items-center justify-between mb-2">
        <div className="flex items-center space-x-2">
          {icon}
          <h3 className="text-sm font-medium text-gray-900">{title}</h3>
        </div>
        {getStatusIcon(status)}
      </div>
      
      <div className="flex items-baseline space-x-2">
        <p className="text-2xl font-semibold text-gray-900">
          {typeof value === 'number' ? value.toLocaleString() : value}
        </p>
        {change !== undefined && (
          <span className={`text-sm font-medium ${
            change >= 0 ? 'text-green-600' : 'text-red-600'
          }`}>
            {change >= 0 ? '+' : ''}{change.toFixed(1)}%
          </span>
        )}
      </div>
      
      {description && (
        <p className="text-sm text-gray-600 mt-1">{description}</p>
      )}
    </div>
  );
};

interface RealtimeChartProps {
  data: Array<{ data: any; timestamp: number }>;
  title: string;
  color: string;
}

const RealtimeChart: React.FC<RealtimeChartProps> = ({ data, title, color }) => {
  const maxValue = Math.max(...data.map(d => Object.values(d.data)[0] as number || 0));
  
  return (
    <div className="bg-white p-4 rounded-lg border border-gray-200">
      <h4 className="text-sm font-medium text-gray-900 mb-3">{title}</h4>
      <div className="flex items-end space-x-1 h-20">
        {data.slice(-20).map((point, index) => {
          const value = Object.values(point.data)[0] as number || 0;
          const height = maxValue > 0 ? (value / maxValue) * 100 : 0;
          
          return (
            <div
              key={index}
              className={`${color} rounded-t`}
              style={{ 
                height: `${height}%`,
                minHeight: '2px',
                width: '100%'
              }}
              title={`${value} at ${new Date(point.timestamp * 1000).toLocaleTimeString()}`}
            />
          );
        })}
      </div>
      <div className="text-xs text-gray-500 mt-1">
        Last 20 data points
      </div>
    </div>
  );
};

const MetricsDashboard: React.FC = () => {
  const [metricsData, setMetricsData] = useState<MetricsData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [refreshInterval, setRefreshInterval] = useState<NodeJS.Timeout | null>(null);

  const fetchMetrics = async () => {
    try {
      const response = await fetch(`${(window as any).acaData.api_url}metrics-dashboard`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': (window as any).acaData.nonce,
        },
        body: JSON.stringify({
          action: 'aca_get_metrics_dashboard'
        })
      });

      if (!response.ok) {
        throw new Error('Failed to fetch metrics');
      }

      const result = await response.json();
      
      if (result.success) {
        setMetricsData(result.data);
        setError(null);
      } else {
        throw new Error(result.message || 'Failed to load metrics');
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : 'An error occurred');
      console.error('Metrics fetch error:', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchMetrics();

    // Set up auto-refresh every 30 seconds
    const interval = setInterval(fetchMetrics, 30000);
    setRefreshInterval(interval);

    return () => {
      if (refreshInterval) {
        clearInterval(refreshInterval);
      }
    };
  }, []);

  const formatBytes = (bytes: number): string => {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
  };

  const formatTime = (seconds: number): string => {
    if (seconds < 1) return `${(seconds * 1000).toFixed(0)}ms`;
    return `${seconds.toFixed(2)}s`;
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <span className="ml-2 text-gray-600">Loading metrics...</span>
      </div>
    );
  }

  if (error) {
    return (
      <div className="bg-red-50 border border-red-200 rounded-lg p-4">
        <div className="flex items-center">
          <AlertTriangle className="w-5 h-5 text-red-600 mr-2" />
          <h3 className="text-sm font-medium text-red-800">Error Loading Metrics</h3>
        </div>
        <p className="text-sm text-red-700 mt-1">{error}</p>
        <button
          onClick={() => {
            setLoading(true);
            setError(null);
            fetchMetrics();
          }}
          className="mt-2 px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700"
        >
          Retry
        </button>
      </div>
    );
  }

  if (!metricsData) {
    return (
      <div className="text-center text-gray-500 py-8">
        No metrics data available
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">Performance Dashboard</h2>
          <p className="text-gray-600">Real-time metrics and KPI tracking</p>
        </div>
        <button
          onClick={fetchMetrics}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center space-x-2"
        >
          <TrendingUp className="w-4 h-4" />
          <span>Refresh</span>
        </button>
      </div>

      {/* Technical Metrics */}
      <div>
        <h3 className="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <Zap className="w-5 h-5 mr-2" />
          Technical Performance
        </h3>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <MetricCard
            title="Bundle Size"
            value={formatBytes(metricsData.technical.bundle_size)}
            icon={<BarChart3 className="w-5 h-5" />}
            status={metricsData.technical.bundle_size > 200 * 1024 ? 'warning' : 'healthy'}
            description="JavaScript bundle size"
          />
          <MetricCard
            title="Load Time"
            value={formatTime(metricsData.technical.load_time)}
            icon={<TrendingUp className="w-5 h-5" />}
            status={metricsData.technical.load_time > 2 ? 'warning' : 'healthy'}
            description="Average page load time"
          />
          <MetricCard
            title="Memory Usage"
            value={formatBytes(metricsData.technical.memory_usage)}
            icon={<BarChart3 className="w-5 h-5" />}
            status={metricsData.technical.memory_usage > 50 * 1024 * 1024 ? 'warning' : 'healthy'}
            description="Current memory usage"
          />
          <MetricCard
            title="Error Rate"
            value={`${metricsData.technical.error_rate.toFixed(2)}%`}
            icon={<AlertTriangle className="w-5 h-5" />}
            status={metricsData.technical.error_rate > 1 ? 'error' : 'healthy'}
            description="API error rate"
          />
        </div>
      </div>

      {/* User Metrics */}
      <div>
        <h3 className="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <Users className="w-5 h-5 mr-2" />
          User Engagement
        </h3>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <MetricCard
            title="Active Today"
            value={metricsData.user.active_today}
            icon={<Users className="w-5 h-5" />}
            status="healthy"
            description="Users active today"
          />
          <MetricCard
            title="Active This Week"
            value={metricsData.user.active_week}
            icon={<Users className="w-5 h-5" />}
            status="healthy"
            description="Users active this week"
          />
          <MetricCard
            title="Retention Rate"
            value={`${metricsData.user.retention_rate.toFixed(1)}%`}
            icon={<TrendingUp className="w-5 h-5" />}
            status={metricsData.user.retention_rate < 70 ? 'warning' : 'healthy'}
            description="Weekly user retention"
          />
          <MetricCard
            title="Satisfaction"
            value={`${metricsData.user.satisfaction.toFixed(1)}/5`}
            icon={<CheckCircle className="w-5 h-5" />}
            status={metricsData.user.satisfaction < 3.5 ? 'warning' : 'healthy'}
            description="Average user rating"
          />
        </div>
      </div>

      {/* Business Metrics */}
      <div>
        <h3 className="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <BarChart3 className="w-5 h-5 mr-2" />
          Business Performance
        </h3>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <MetricCard
            title="Content Generated"
            value={metricsData.business.content_generated}
            icon={<BarChart3 className="w-5 h-5" />}
            status="healthy"
            description="Content pieces today"
          />
          <MetricCard
            title="Conversion Rate"
            value={`${metricsData.business.conversion_rate.toFixed(1)}%`}
            icon={<TrendingUp className="w-5 h-5" />}
            status={metricsData.business.conversion_rate < 10 ? 'warning' : 'healthy'}
            description="Visitor to creator conversion"
          />
          <MetricCard
            title="Feature Adoption"
            value={`${metricsData.business.feature_adoption.toFixed(1)}%`}
            icon={<Users className="w-5 h-5" />}
            status={metricsData.business.feature_adoption < 50 ? 'warning' : 'healthy'}
            description="Users using features"
          />
          <MetricCard
            title="Revenue Impact"
            value={`$${metricsData.business.revenue_impact.toLocaleString()}`}
            icon={<TrendingUp className="w-5 h-5" />}
            status="healthy"
            description="Estimated value generated"
          />
        </div>
      </div>

      {/* Real-time Charts */}
      <div>
        <h3 className="text-lg font-semibold text-gray-900 mb-4">Real-time Activity</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {metricsData.realtime.page_view && (
            <RealtimeChart
              data={metricsData.realtime.page_view}
              title="Page Views"
              color="bg-blue-500"
            />
          )}
          {metricsData.realtime.api_call && (
            <RealtimeChart
              data={metricsData.realtime.api_call}
              title="API Calls"
              color="bg-green-500"
            />
          )}
          {metricsData.realtime.content_generated && (
            <RealtimeChart
              data={metricsData.realtime.content_generated}
              title="Content Generated"
              color="bg-purple-500"
            />
          )}
          {metricsData.realtime.user_action && (
            <RealtimeChart
              data={metricsData.realtime.user_action}
              title="User Actions"
              color="bg-yellow-500"
            />
          )}
        </div>
      </div>

      {/* System Status */}
      <div className="bg-white p-6 rounded-lg border border-gray-200">
        <h3 className="text-lg font-semibold text-gray-900 mb-4">System Status</h3>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div className="flex items-center space-x-3">
            {metricsData.technical.status === 'healthy' ? (
              <CheckCircle className="w-6 h-6 text-green-600" />
            ) : (
              <AlertTriangle className="w-6 h-6 text-yellow-600" />
            )}
            <div>
              <p className="font-medium text-gray-900">Technical</p>
              <p className="text-sm text-gray-600 capitalize">{metricsData.technical.status}</p>
            </div>
          </div>
          
          <div className="flex items-center space-x-3">
            {metricsData.user.status === 'healthy' ? (
              <CheckCircle className="w-6 h-6 text-green-600" />
            ) : (
              <AlertTriangle className="w-6 h-6 text-yellow-600" />
            )}
            <div>
              <p className="font-medium text-gray-900">User Experience</p>
              <p className="text-sm text-gray-600 capitalize">{metricsData.user.status}</p>
            </div>
          </div>
          
          <div className="flex items-center space-x-3">
            {metricsData.business.status === 'healthy' ? (
              <CheckCircle className="w-6 h-6 text-green-600" />
            ) : (
              <AlertTriangle className="w-6 h-6 text-yellow-600" />
            )}
            <div>
              <p className="font-medium text-gray-900">Business</p>
              <p className="text-sm text-gray-600 capitalize">{metricsData.business.status}</p>
            </div>
          </div>
        </div>
      </div>

      {/* Auto-refresh indicator */}
      <div className="text-center text-sm text-gray-500">
        <p>Dashboard auto-refreshes every 30 seconds</p>
        <p className="text-xs">Last updated: {new Date().toLocaleTimeString()}</p>
      </div>
    </div>
  );
};

export default MetricsDashboard;