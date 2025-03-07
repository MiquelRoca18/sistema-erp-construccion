/**
 * Genera una función con debounce que limita la frecuencia de ejecución
 * @param fn Función a ejecutar con debounce
 * @param delay Retraso en milisegundos
 * @returns Función con debounce
 */
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
  
  /**
   * Genera una función con throttle que limita la frecuencia de ejecución
   * @param fn Función a ejecutar con throttle
   * @param limit Límite de tiempo en milisegundos
   * @returns Función con throttle
   */
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
  
  /**
   * Función para cachear datos en localStorage
   * @param key Clave de almacenamiento
   * @param data Datos a almacenar
   * @param ttl Tiempo de vida en milisegundos (10 minutos por defecto)
   */
  export const setLocalStorageWithExpiry = (key: string, data: any, ttl: number = 600000) => {
    const item = {
      value: data,
      expiry: Date.now() + ttl
    };
    localStorage.setItem(key, JSON.stringify(item));
  };
  
  /**
   * Función para obtener datos cacheados de localStorage
   * @param key Clave de almacenamiento
   * @returns Datos almacenados o null si no existen o han expirado
   */
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