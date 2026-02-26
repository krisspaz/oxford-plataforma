
const ProgressBar = ({ progress }) => {
    return (
        <div className="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
            <div
                className="bg-indigo-600 h-2.5 rounded-full transition-all duration-500 ease-out"
                style={{ width: `${Math.min(100, Math.max(0, progress))}%` }}
            ></div>
        </div>
    );
};

export default ProgressBar;
