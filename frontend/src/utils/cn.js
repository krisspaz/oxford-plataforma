/**
 * ClassName Merge Utility
 * Combines class names and filters out falsy values
 * @param  {...string} classes - Class names to merge
 * @returns {string} - Merged class names
 */
export function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

export default cn;
