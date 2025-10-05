/**
 * ClassObserver - 特定のクラスの変更を監視するモジュール
 * 
 * 使用例:
 * const observer = new ClassObserver('.target-element', {
 *   classNames: ['active', 'visible'],
 *   onClassAdded: (element, className) => console.log(`${className} added`),
 *   onClassRemoved: (element, className) => console.log(`${className} removed`),
 *   onClassChanged: (element, addedClasses, removedClasses) => console.log('Classes changed')
 * });
 */

export class ClassObserver {
  /**
   * @param {string|Element|NodeList} target - 監視対象の要素（セレクタ、Element、NodeList）
   * @param {Object} options - オプション設定
   * @param {string[]} options.classNames - 監視するクラス名の配列（省略時は全クラスを監視）
   * @param {Function} options.onClassAdded - クラスが追加された時のコールバック
   * @param {Function} options.onClassRemoved - クラスが削除された時のコールバック
   * @param {Function} options.onClassChanged - クラスが変更された時のコールバック
   * @param {boolean} options.immediate - 初期化時に現在のクラス状態をチェックするか
   */
  constructor(target, options = {}) {
    this.options = {
      classNames: null, // null = 全クラスを監視
      onClassAdded: null,
      onClassRemoved: null,
      onClassChanged: null,
      immediate: false,
      ...options
    };

    this.observers = new Map();
    this.previousClasses = new Map();
    
    this.init(target);
  }

  /**
   * 初期化処理
   * @param {string|Element|NodeList} target 
   */
  init(target) {
    const elements = this.getElements(target);
    
    elements.forEach(element => {
      this.observeElement(element);
      
      // 初期状態のクラス情報を保存
      const currentClasses = new Set(element.classList);
      this.previousClasses.set(element, currentClasses);
      
      // immediate オプションが true の場合、初期状態をチェック
      if (this.options.immediate && this.options.onClassChanged) {
        this.options.onClassChanged(element, currentClasses, new Set());
      }
    });
  }

  /**
   * 要素の取得
   * @param {string|Element|NodeList} target 
   * @returns {Element[]}
   */
  getElements(target) {
    if (typeof target === 'string') {
      return Array.from(document.querySelectorAll(target));
    } else if (target instanceof Element) {
      return [target];
    } else if (target instanceof NodeList) {
      return Array.from(target);
    } else {
      console.error('ClassObserver: Invalid target type');
      return [];
    }
  }

  /**
   * 要素の監視を開始
   * @param {Element} element 
   */
  observeElement(element) {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach(mutation => {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          this.handleClassChange(element);
        }
      });
    });

    observer.observe(element, {
      attributes: true,
      attributeFilter: ['class']
    });

    this.observers.set(element, observer);
  }

  /**
   * クラス変更の処理
   * @param {Element} element 
   */
  handleClassChange(element) {
    const currentClasses = new Set(element.classList);
    const previousClasses = this.previousClasses.get(element) || new Set();
    
    // 追加されたクラスを検出
    const addedClasses = new Set([...currentClasses].filter(cls => !previousClasses.has(cls)));
    
    // 削除されたクラスを検出
    const removedClasses = new Set([...previousClasses].filter(cls => !currentClasses.has(cls)));
    
    // 監視対象のクラス名が指定されている場合はフィルタリング
    const filteredAddedClasses = this.filterClasses(addedClasses);
    const filteredRemovedClasses = this.filterClasses(removedClasses);
    
    // コールバック関数の実行
    if (filteredAddedClasses.size > 0 || filteredRemovedClasses.size > 0) {
      // 個別のクラス変更コールバック
      filteredAddedClasses.forEach(className => {
        if (this.options.onClassAdded) {
          this.options.onClassAdded(element, className, currentClasses);
        }
      });
      
      filteredRemovedClasses.forEach(className => {
        if (this.options.onClassRemoved) {
          this.options.onClassRemoved(element, className, currentClasses);
        }
      });
      
      // 全体のクラス変更コールバック
      if (this.options.onClassChanged) {
        this.options.onClassChanged(element, filteredAddedClasses, filteredRemovedClasses, currentClasses);
      }
    }
    
    // 現在のクラス状態を保存
    this.previousClasses.set(element, currentClasses);
  }

  /**
   * 監視対象のクラスでフィルタリング
   * @param {Set} classes 
   * @returns {Set}
   */
  filterClasses(classes) {
    if (!this.options.classNames) {
      return classes;
    }
    
    return new Set([...classes].filter(cls => this.options.classNames.includes(cls)));
  }

  /**
   * 特定の要素の監視を停止
   * @param {Element} element 
   */
  unobserveElement(element) {
    const observer = this.observers.get(element);
    if (observer) {
      observer.disconnect();
      this.observers.delete(element);
      this.previousClasses.delete(element);
    }
  }

  /**
   * 全ての監視を停止
   */
  disconnect() {
    this.observers.forEach(observer => observer.disconnect());
    this.observers.clear();
    this.previousClasses.clear();
  }

  /**
   * 新しい要素を監視対象に追加
   * @param {string|Element|NodeList} target 
   */
  addTarget(target) {
    const elements = this.getElements(target);
    elements.forEach(element => {
      if (!this.observers.has(element)) {
        this.observeElement(element);
        const currentClasses = new Set(element.classList);
        this.previousClasses.set(element, currentClasses);
      }
    });
  }

  /**
   * 監視対象から要素を削除
   * @param {string|Element|NodeList} target 
   */
  removeTarget(target) {
    const elements = this.getElements(target);
    elements.forEach(element => {
      this.unobserveElement(element);
    });
  }

  /**
   * 現在監視中の要素一覧を取得
   * @returns {Element[]}
   */
  getObservedElements() {
    return Array.from(this.observers.keys());
  }

  /**
   * 特定の要素の現在のクラス状態を取得
   * @param {Element} element 
   * @returns {Set|null}
   */
  getCurrentClasses(element) {
    return this.previousClasses.get(element) || null;
  }
}