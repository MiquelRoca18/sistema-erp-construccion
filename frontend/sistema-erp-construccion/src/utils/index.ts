export const debounce = (fn: Function, delay: number) => {
    let timeout: ReturnType<typeof setTimeout> | null = null;
    
    return (...args: any[]) => {
      if (timeout) {
        clearTimeout(timeout);
      }
      
      timeout = setTimeout(() => {
        fn(...args);
        timeout = null;
      }, delay);
    };
  };
  
  export const throttle = (fn: Function, limit: number) => {
    let inThrottle: boolean = false;
    
    return (...args: any[]) => {
      if (!inThrottle) {
        fn(...args);
        inThrottle = true;
        setTimeout(() => {
          inThrottle = false;
        }, limit);
      }
    };
  };

  export const setLocalStorageWithExpiry = (key: string, data: any, ttl: number = 600000) => {
    const item = {
      value: data,
      expiry: Date.now() + ttl
    };
    localStorage.setItem(key, JSON.stringify(item));
  };
  
  export const getLocalStorageWithExpiry = (key: string) => {
    const itemStr = localStorage.getItem(key);
    
    if (!itemStr) return null;
    
    try {
      const item = JSON.parse(itemStr);
      const isExpired = Date.now() > item.expiry;
      
      if (isExpired) {
        localStorage.removeItem(key);
        return null;
      }
      
      return item.value;
    } catch (e) {
      localStorage.removeItem(key);
      return null;
    }
  };