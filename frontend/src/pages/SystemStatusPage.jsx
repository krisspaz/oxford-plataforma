import React, { useEffect, useState } from 'react';
import Layout from '../components/Layout';
import { useAuth } from '../contexts/AuthContext';
import { Activity, Database, Server, HardDrive, RefreshCw } from 'lucide-react';

const StatusCard = ({ title, icon: Icon, status, details }) => {
    const isUp = status === 'up';
    return (
        <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div className="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 className="text-sm font-medium text-gray-700 dark:text-gray-200">{title}</h3>
                <Icon className={`h-4 w-4 ${isUp ? 'text-green-500' : 'text-red-500'}`} />
            </div>
            <div>
                <div className={`text-2xl font-bold ${isUp ? 'text-green-600' : 'text-red-600'}`}>
                    {isUp ? 'Operational' : 'Down'}
                </div>
                {details && <p className="text-xs text-gray-500 dark:text-gray-400">{details}</p>}
            </div>
        </div>
    );
};

const SystemStatusPage = () => {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const { token } = useAuth();

    const fetchData = async () => {
        try {
            const response = await fetch('/api/system/status', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            if (!response.ok) throw new Error('Failed to fetch status');
            const result = await response.json();
            setData(result);
            setError(null);
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchData();
        const interval = setInterval(fetchData, 5000); // Poll every 5 seconds
        return () => clearInterval(interval);
    }, [token]);

    return (
        <Layout>
            <div className="p-8 space-y-8 bg-gray-50 min-h-screen dark:bg-gray-900">
                <div className="flex justify-between items-center">
                    <h1 className="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">System Status</h1>
                    <span className="text-sm text-gray-500 flex items-center">
                        <RefreshCw className="mr-2 h-4 w-4 animate-spin" /> Live Updating (5s)
                    </span>
                </div>

                {error && (
                    <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <strong className="font-bold">Error: </strong>
                        <span className="block sm:inline">{error}</span>
                    </div>
                )}

                {data && (
                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <StatusCard
                            title="Database"
                            icon={Database}
                            status={data.services.database}
                            details="PostgreSQL 16"
                        />
                        <StatusCard
                            title="Object Storage"
                            icon={HardDrive}
                            status={data.services.minio}
                            details="MinIO Bucket [oxford-storage]"
                        />
                        <StatusCard
                            title="Redis Cache"
                            icon={Server}
                            status={data.services.redis}
                            details="Queue & Cache"
                        />
                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">Job Queue</CardTitle>
                                <Activity className="h-4 w-4 text-blue-500" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">
                                    {data.queues.async} <span className="text-sm font-normal text-gray-500">pending</span>
                                </div>
                                <p className="text-xs text-muted-foreground">
                                    Processed by Workers
                                </p>
                            </CardContent>
                        </Card>
                    </div>
                )}
            </div>
        </Layout>
    );
};

export default SystemStatusPage;
