import React, { createContext, useContext, useReducer, ReactNode } from 'react';
import type { StyleGuide, ContentIdea, Draft, View, AppSettings, ActivityLog } from '../types';

// Define the app state interface
interface AppState {
  view: View;
  styleGuide: StyleGuide | null;
  ideas: ContentIdea[];
  posts: Draft[];
  selectedDraft: Draft | null;
  activityLogs: ActivityLog[];
  settings: AppSettings;
  drafts: Draft[];
  publishedPosts: Draft[];
  publishingId: string | null;
  isLoading: { [key: string]: boolean };
  isSidebarOpen: boolean;
  toast: { message: string; type: 'success' | 'error' | 'warning' | 'info' } | null;
}

// Define action types
type AppAction =
  | { type: 'SET_VIEW'; payload: View }
  | { type: 'SET_STYLE_GUIDE'; payload: StyleGuide | null }
  | { type: 'SET_IDEAS'; payload: ContentIdea[] }
  | { type: 'ADD_IDEAS'; payload: ContentIdea[] }
  | { type: 'UPDATE_IDEA'; payload: { id: number; updates: Partial<ContentIdea> } }
  | { type: 'DELETE_IDEA'; payload: number }
  | { type: 'SET_POSTS'; payload: Draft[] }
  | { type: 'SET_SELECTED_DRAFT'; payload: Draft | null }
  | { type: 'SET_ACTIVITY_LOGS'; payload: ActivityLog[] }
  | { type: 'ADD_ACTIVITY_LOG'; payload: ActivityLog }
  | { type: 'SET_SETTINGS'; payload: AppSettings }
  | { type: 'SET_DRAFTS'; payload: Draft[] }
  | { type: 'SET_PUBLISHED_POSTS'; payload: Draft[] }
  | { type: 'SET_PUBLISHING_ID'; payload: string | null }
  | { type: 'SET_LOADING'; payload: { key: string; value: boolean } }
  | { type: 'SET_SIDEBAR_OPEN'; payload: boolean }
  | { type: 'SET_TOAST'; payload: { message: string; type: 'success' | 'error' | 'warning' | 'info' } | null };

// Initial state
const initialState: AppState = {
  view: 'dashboard',
  styleGuide: null,
  ideas: [],
  posts: [],
  selectedDraft: null,
  activityLogs: [],
  settings: {
    mode: 'manual',
    autoPublish: false,
    searchConsoleUser: null,
    gscClientId: '',
    gscClientSecret: '',
    imageSourceProvider: 'pexels',
    aiImageStyle: 'photorealistic',
    googleCloudProjectId: '',
    googleCloudLocation: 'us-central1',
    pexelsApiKey: '',
    unsplashApiKey: '',
    pixabayApiKey: '',
    seoPlugin: 'none',
    geminiApiKey: '',
    semiAutoIdeaFrequency: 'weekly',
    fullAutoPostCount: 1,
    fullAutoFrequency: 'daily',
    contentAnalysisFrequency: 'weekly',
  },
  drafts: [],
  publishedPosts: [],
  publishingId: null,
  isLoading: {},
  isSidebarOpen: true,
  toast: null,
};

// Reducer function
function appReducer(state: AppState, action: AppAction): AppState {
  switch (action.type) {
    case 'SET_VIEW':
      return { ...state, view: action.payload };
    case 'SET_STYLE_GUIDE':
      return { ...state, styleGuide: action.payload };
    case 'SET_IDEAS':
      return { ...state, ideas: action.payload };
    case 'ADD_IDEAS':
      return { ...state, ideas: [...state.ideas, ...action.payload] };
    case 'UPDATE_IDEA':
      return {
        ...state,
        ideas: state.ideas.map(idea =>
          idea.id === action.payload.id
            ? { ...idea, ...action.payload.updates }
            : idea
        ),
      };
    case 'DELETE_IDEA':
      return {
        ...state,
        ideas: state.ideas.filter(idea => idea.id !== action.payload),
      };
    case 'SET_POSTS':
      return { ...state, posts: action.payload };
    case 'SET_SELECTED_DRAFT':
      return { ...state, selectedDraft: action.payload };
    case 'SET_ACTIVITY_LOGS':
      return { ...state, activityLogs: action.payload };
    case 'ADD_ACTIVITY_LOG':
      return { ...state, activityLogs: [action.payload, ...state.activityLogs] };
    case 'SET_SETTINGS':
      return { ...state, settings: action.payload };
    case 'SET_DRAFTS':
      return { ...state, drafts: action.payload };
    case 'SET_PUBLISHED_POSTS':
      return { ...state, publishedPosts: action.payload };
    case 'SET_PUBLISHING_ID':
      return { ...state, publishingId: action.payload };
    case 'SET_LOADING':
      return {
        ...state,
        isLoading: { ...state.isLoading, [action.payload.key]: action.payload.value },
      };
    case 'SET_SIDEBAR_OPEN':
      return { ...state, isSidebarOpen: action.payload };
    case 'SET_TOAST':
      return { ...state, toast: action.payload };
    default:
      return state;
  }
}

// Create context
const AppContext = createContext<{
  state: AppState;
  dispatch: React.Dispatch<AppAction>;
} | null>(null);

// Provider component
interface AppProviderProps {
  children: ReactNode;
}

export const AppProvider: React.FC<AppProviderProps> = ({ children }) => {
  const [state, dispatch] = useReducer(appReducer, initialState);

  return (
    <AppContext.Provider value={{ state, dispatch }}>
      {children}
    </AppContext.Provider>
  );
};

// Custom hook to use the context
export const useAppContext = () => {
  const context = useContext(AppContext);
  if (!context) {
    throw new Error('useAppContext must be used within an AppProvider');
  }
  return context;
};

// Action creators for common operations
export const appActions = {
  setView: (view: View) => ({ type: 'SET_VIEW' as const, payload: view }),
  setStyleGuide: (styleGuide: StyleGuide | null) => ({ type: 'SET_STYLE_GUIDE' as const, payload: styleGuide }),
  setIdeas: (ideas: ContentIdea[]) => ({ type: 'SET_IDEAS' as const, payload: ideas }),
  addIdeas: (ideas: ContentIdea[]) => ({ type: 'ADD_IDEAS' as const, payload: ideas }),
  updateIdea: (id: number, updates: Partial<ContentIdea>) => ({ type: 'UPDATE_IDEA' as const, payload: { id, updates } }),
  deleteIdea: (id: number) => ({ type: 'DELETE_IDEA' as const, payload: id }),
  setSettings: (settings: AppSettings) => ({ type: 'SET_SETTINGS' as const, payload: settings }),
  setLoading: (key: string, value: boolean) => ({ type: 'SET_LOADING' as const, payload: { key, value } }),
  setToast: (toast: { message: string; type: 'success' | 'error' | 'warning' | 'info' } | null) => ({ type: 'SET_TOAST' as const, payload: toast }),
};